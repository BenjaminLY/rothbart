<?php

namespace App\Services;
use Carbon\Carbon;

use App\Models\AccessRight;
use App\Models\Video;

class RightAccessService
{
    public function verify($reference, $user_id)
    {
        $video = Video::where('reference', $reference)->firstOrFail();

        $accessRights = AccessRight::select(['type', 'created_at'])
        ->where('movie_id', $video->movie_id)
        ->where('user_id', $user_id)
        ->groupBy('type')
        ->orderBy('created_at', 'DESC')
        ->get();

        $rights = [];
        foreach ($accessRights as $accessRight)
        {
            switch ($accessRight->type)
            {
                case 'stream':
                // the streaming right access, expires 7 days after purchase, if it's more than 7 days
                // we delete the current right access
                $createdAt = Carbon::parse($accessRight->created_at);
                $now = Carbon::now();
                $length = $createdAt->diffInDays($now);

                if ($length > 7)
                {
                    $accessRight->delete();
                }
                else
                {
                    array_push($rights, 'stream');
                }
                break;
                case 'download':
                // the download right access, expires 2 days after purchase, if it's more than 2 days
                // we delete the current right access
                $createdAt = Carbon::parse($accessRight->created_at);
                $now = Carbon::now();
                $length = $createdAt->diffInDays($now);

                if ($length > 2)
                {
                    $accessRight->delete();
                }
                else
                {
                    array_push($rights, 'download');
                }
                break;
                case 'download_stream':
                $createdAt = Carbon::parse($accessRight->created_at);
                $now = Carbon::now();
                $length = $createdAt->diffInDays($now);

                // the streaming right access, expires 7 days after purchase, if it's more than 7 days
                // we delete the current right access
                if ($length > 7)
                {
                    $accessRight->delete();
                }
                // the download right access, expires 2 days after purchase, if it's more than 2 days
                // we replace the current right access by stream only
                elseif ($length > 2)
                {
                    $accessRight->type = 'stream';
                    $accessRight->save();
                    array_push($rights, 'stream');
                }
                else
                {
                    array_push($rights, 'download_stream');
                }
                break;
            }
        }

        return $rights;
    }
}
