<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client as GuzzleClient;

use App\Mail\RegisterConfirmation;
use App\Mail\PaymentSuccessStream;
use App\Mail\PaymentSuccessDownload;
use App\Models\AccessRight;
use App\Models\Movie;
use App\Models\Plan;
use App\Models\Transaction;
use App\Models\Video;
use App\User;

use App\Services\RightAccessService;

use Carbon\Carbon;
use Verotel\FlexPay\Brand;
use Verotel\FlexPay\Client;

class PaymentController extends Controller
{
    public function paymentUrl(Request $request, RightAccessService $rightAccessService)
    {
        $rules = [
            'movieId' => 'required|exists:movies,id',
            'planId' => 'required|exists:plans,id'
        ];

        if (empty($request->user()))
        {
            $rules['email'] = 'email|required';
            $rules['password'] = 'required|min:6';
        }

        $movie = Movie::find($request->input('movieId'));
        $video = Video::where('movie_id', $movie->id)
        ->where('type', 'movie')->first();

        $this->validate($request, $rules);

        if (empty($request->user()))
        {
            // check if the specified credentials has been already created
            $user = User::where('email', $request->input('email'))->first();

            if (empty($user)) // non existing user: create a new one
            {
                $confirmationCode = uniqid();
                $data = [
                    'username' => $request->input('email'),
                    'email' => $request->input('email'),
                    'password' => app('hash')->make($request->input('password')),
                    'confirmation' => $confirmationCode,
                    'status' => 'non_active'
                ];

                if (empty($user))
                {
                    $user = User::create($data);

                    $protocol = $request->secure() ? 'https://' : 'http://';
                    $mailDatas = [
                        'movie_name' => $movie->title,
                        'registration_confirmation_url' => $protocol . $movie->domain . '/auth/confirmation/' . $confirmationCode
                    ];
                    // send mail to ask confirmation
                    Mail::send('email-confirm-registration', ['data' => $mailDatas], function ($mail) use ($user) {
                       $mail->to($user->email)->subject('Confirmation d\'inscription');
                    });
                }
            }

            // sign in user
            $http = new GuzzleClient;
            try {
                $protocol = $request->secure() ? 'https://' : 'http://';
                $response = $http->post($protocol . $request->getHost() . '/oauth/token', [
                    'form_params' => [
                        'grant_type' => 'password',
                        'client_id' => getenv('PASSPORT_ID_PASSWORD_ACCESS'),
                        'client_secret' => getenv('PASSPORT_SECRET_PASSWORD_ACCESS'),
                        'username' => $request->input('email'),
                        'password' => $request->input('password'),
                        'scope' => '',
                    ],
                ]);
                $token_datas = json_decode((string) $response->getBody());
            } catch (GuzzleException $e) {
                $response = $e->getResponse();
                return response(json_decode((string) $response->getBody(), true), 401);
            }
        }
        else
        {
            $user = $request->user();
            $token_datas = null;
        }

        // check if current User can get access to the video for streaming
        $rights = $rightAccessService->verify($video->reference, $user->id);

        $plan = Plan::find($request->input('planId'));
        $accessRight = false;
        switch ($plan->type)
        {
            case 'download_stream':
                $accessRight = in_array('download_stream', $rights);
            break;
            case 'stream':
                $accessRight = in_array('stream', $rights) || in_array('download_stream', $rights);
            break;
        }

        if ($accessRight)
        {
            return response([
                'token_datas' => $token_datas,
                'payment_url' => null
            ], 200);
        }
        else
        {
            $brand = Brand::create_from_merchant_id(env('VEROTEL_CUSTOMER_ID'));
            $flexpayClient = new Client(env('VEROTEL_SHOP_ID'), env('VEROTEL_SIGNATURE'), $brand);

            $reference = uniqid();
            // create transaction
            $transaction = Transaction::create([
                'reference' => $reference,
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'movie_id' => $request->input('movieId'),
                'status' => 'pending'
            ]);

            return response([
                'token_datas' => $token_datas,
                'payment_url' => $flexpayClient->get_purchase_URL([
                    "email" => $user->email,
                    "priceAmount" => $plan->price,
                    "priceCurrency" => "EUR",
                    "referenceID" => $reference,
                    "description" => $plan->title
                ])
            ], 200);
        }
    }

    public function callback(Request $request)
    {
        $brand = Brand::create_from_merchant_id(env('VEROTEL_CUSTOMER_ID'));
        $flexpayClient = new Client(env('VEROTEL_SHOP_ID'), env('VEROTEL_SIGNATURE'), $brand);

        // save transaction
        if (!$flexpayClient->validate_signature($_GET))
        {
            return response('ERROR - Invalid signature!', 500);
        }
        else
        {
            // update transaction status
            $transaction = Transaction::where('reference', $request->input('referenceID'))->first();

            $transaction->update([
                'status' => 'paid',
                'paid_at' => Carbon::now(),
                'transaction_id' => $request->input('transactionID'),
            ]);

            // get paid plan type
            $plan = Plan::find($transaction->plan_id);

            // update user status
            $user = User::find($transaction->user_id);

            // give to user the access to the video
            AccessRight::create([
                'user_id' => $transaction->user_id,
                'movie_id' => $transaction->movie_id,
                'type' => $plan->type
            ]);

            // send notification to the user
            $movie = Movie::find($transaction->movie_id);
            $mailDatas = [
                'movie_name' => $movie->title,
                'home_url' => 'https://' . $movie->domain
            ];
            if ($plan->type === 'download_stream')
            {
                Mail::send('email-success-payment-download', ['data' => $mailDatas], function ($mail) use ($user) {
                   $mail->to($user->email)->subject('Confirmation de paiement');
                });
            }
            elseif ($plan->type === 'stream')
            {
                Mail::send('email-success-payment-stream', ['data' => $mailDatas], function ($mail) use ($user) {
                   $mail->to($user->email)->subject('Confirmation de paiement');
                });
            }

            return response('OK', 200);
        }
    }

    public function check(Request $request)
    {
        $brand = Brand::create_from_merchant_id(env('VEROTEL_CUSTOMER_ID'));
        $flexpayClient = new Client(env('VEROTEL_SHOP_ID'), env('VEROTEL_SIGNATURE'), $brand);

        // save transaction
        if (!$flexpayClient->validate_signature($_GET))
        {
            return response(null, 403);
        }
        else
        {
            // check transaction status
            $transaction = Transaction::where('reference', $request->input('referenceID'))->first();

            if (!empty($transaction) && $transaction->status === 'paid')
            {
                return response(null, 200);
            }
            else
            {
                return response(null, 403);
            }
        }
    }
}
