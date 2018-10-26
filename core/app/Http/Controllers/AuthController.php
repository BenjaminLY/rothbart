<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use App\User;

class AuthController extends Controller
{
    /**
     * Obtain access token JWT.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function signin(Request $request)
    {
        $http = new Client;
        try {
            $protocol = $request->secure() ? 'https://' : 'http://';
            $response = $http->post($protocol . $request->getHost() . '/oauth/token', [
                'form_params' => [
                    'grant_type' => 'password',
                    'client_id' => getenv('PASSPORT_ID_PASSWORD_ACCESS'),
                    'client_secret' => getenv('PASSPORT_SECRET_PASSWORD_ACCESS'),
                    'username' => $request->input('username'),
                    'password' => $request->input('password'),
                    'scope' => '',
                ],
            ]);
            return response(json_decode((string) $response->getBody(), true), 200);
        } catch (GuzzleException $e) {
            $response = $e->getResponse();
            return response(json_decode((string) $response->getBody(), true), 401);
        }
    }

    /**
     * Refresh access token JWT.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function refresh(Request $request)
    {
        $http = new Client;

        try {
            $protocol = $request->secure() ? 'https://' : 'http://';
            $response = $http->post($protocol . $request->getHost() . '/oauth/token', [
                'form_params' => [
                    'grant_type' => 'refresh_token',
                    'client_id' => getenv('PASSPORT_ID_PASSWORD_ACCESS'),
                    'client_secret' => getenv('PASSPORT_SECRET_PASSWORD_ACCESS'),
                    'refresh_token' => $request->refresh_token,
                    'scope' => '',
                ],
            ]);

           return response(json_decode((string) $response->getBody(), true), 200);
        } catch (GuzzleException $e) {
            $response = $e->getResponse();
            return response(json_decode((string) $response->getBody(), true), 401);
        }
    }

    /**
     * Return user info
     */
    public function me(Request $request)
    {
        return $request->user();
    }

    /**
     * Valid registration
     */
    public function confirmation(Request $request, $confirmation)
    {
        $user = User::where('confirmation', $confirmation)->firstOrFail();

        $user->confirmation = null;
        $user->status = 'active';
        $user->save();

        return response(null, 200);
    }
}
