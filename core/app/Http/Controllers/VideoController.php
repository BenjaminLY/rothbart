<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Symfony\Component\Finder\Finder;
use Carbon\Carbon;

use App\Services\RightAccessService;
use App\Services\VideoService;

use App\Models\AccessRight;
use App\Models\TemporaryVideo;
use App\Models\Video;
use App\Models\Movie;

class VideoController extends Controller
{
    // get videos listing
    public function index()
    {
        if ($request->user()->is_admin !== 1)
        {
            return response(null, 403);
        }

        return Video::paginate();
    }

    // get files listing
    public function getFiles(Request $request)
    {
        if ($request->user()->is_admin !== 1)
        {
            return response(null, 403);
        }

        $finder = new Finder();
        $finder->files()->in(env('VIDEO_PATH'));

        $files = [];
        foreach ($finder as $file) {
            array_push($files, [
                'path' => $file->getRealPath(),
                'filename' => $file->getRelativePathname()
            ]);
        }
        return response($files, 202);
    }

    // create a new video
    public function store(Request $request)
    {
        if ($request->user()->is_admin !== 1)
        {
            return response(null, 403);
        }

        $rules = [
            'movie_id' => 'required|exists:movies,id',
            'path' => 'required',
            'type' => 'required|in:trailer,movie'
        ];

        $this->validate($request, $rules);

        if (!file_exists($request->input('path')))
        {
            return response(['errors' => [['path' => 'File doesn\'t exist.']]], 422);
        }

        $video = Video::create([
            'movie_id' => $request->input('movie_id'),
            'reference' => uniqid(),
            'path' => $request->input('path'),
            'type' => $request->input('title')
        ]);

        return response(['resource_id' => $video->id, 200]);
    }

    public function show($id)
    {
        if ($request->user()->is_admin !== 1)
        {
            return response(null, 403);
        }

        return Video::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        if ($request->user()->is_admin !== 1)
        {
            return response(null, 403);
        }

        $rules = [
            'path' => 'required',
            'type' => 'required|in:trailer,movie'
        ];

        $this->validate($request, $rules);

        if (!file_exists($request->input('path')))
        {
            return response(['errors' => [['path' => 'File doesn\'t exist.']]], 422);
        }

        Video::findOrFail($id)->update([
            'path' => $request->input('path'),
            'type' => $request->input('title')
        ]);

        return response(204);
    }

    public function rightAccess(Request $request, RightAccessService $rightAccessService, $reference)
    {
        $rights = $rightAccessService->verify($reference, $request->user()->id);

        if (!empty($rights))
        {
            return response($rights, 200);
        }
        else
        {
            return response(null, 403);
        }
    }

    public function generate(Request $request, RightAccessService $rightAccessService)
    {
        $this->validate($request, ['r' => 'required|in:download,stream,download_stream']);

        $video = Video::where('reference', $request->input('m'))->firstOrFail();

        // check if current User can get access to the video for streaming
        $rights = $rightAccessService->verify($video->reference, $request->user()->id);

        $accessRight = in_array($request->input('r'), $rights) || in_array('download_stream', $rights);

        if (empty($accessRight))
        {
            return response(null, 403);
        }

        // generate temporary reference
        $reference = uniqid();
        TemporaryVideo::where('user_id', $request->user()->id)->delete();
        TemporaryVideo::create([
            'user_id' => $request->user()->id,
            'video_id' => $video->id,
            'reference' => $reference,
            'movie_id' => $video->movie_id
        ]);

        return response(['reference' => $reference], 200);
    }

    public function play(Request $request, VideoService $videoService, RightAccessService $rightAccessService)
    {
        switch ($request->input('t'))
        {
            case 'trailer':
                $video = Video::where('reference', $request->input('m'))
                ->where('type', 'trailer')
                ->firstOrFail();
                return $videoService->stream($video->path);
                break;
            case 'movie':
                $temporaryVideo = TemporaryVideo::where('reference', $request->input('m'))
                ->firstOrFail();

                $video = Video::find($temporaryVideo->video_id);

                // check if current User can get access to the video for streaming
                $rights = $rightAccessService->verify($video->reference, $temporaryVideo->user_id);

                $accessRight = in_array('stream', $rights) || in_array('download_stream', $rights);

                if (empty($accessRight))
                {
                    return response(null, 403);
                }
                else
                {
                    $video = Video::find($temporaryVideo->video_id);
                    return $videoService->stream($video->path);
                }
                break;
        }
    }

    public function download(Request $request, RightAccessService $rightAccessService)
    {
        $temporaryVideo = TemporaryVideo::where('reference', $request->input('m'))
        ->firstOrFail();

        $video = Video::find($temporaryVideo->video_id);

        // check if current User can get access to the video for streaming
        $rights = $rightAccessService->verify($video->reference, $temporaryVideo->user_id);

        // delete temporary reference
        $temporaryVideo->delete();

        // check if current User can get access to the video for download
        $accessRight = in_array('download', $rights) || in_array('download_stream', $rights);

        if (!empty($accessRight))
        {
            setlocale(LC_ALL, 'en_US.UTF8');

            $movie = Movie::find($video->movie_id);

            return response()->download($video->path, $video->reference . '.mp4');
        }
        else
        {
            return response(null, 403);
        }
    }
}
