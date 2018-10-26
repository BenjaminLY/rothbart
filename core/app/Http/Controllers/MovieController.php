<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Movie;

class MovieController extends Controller
{
    // get movie linked to the specified domain
    public function index(Request $request)
    {
        if (empty($request->user()) || $request->user()->is_admin !== 1)
        {
            return response(null, 403);
        }

        $query = Movie::with([
            'videos' => function ($query) use ($request) {
                $query->select(['id', 'movie_id', 'reference', 'type', 'path']);

                if (!empty($request->input('type')))
                {
                    $query->where('type', $request->input('type'));
                }
            }
        ]);

        if (!empty($request->input('keyword')))
        {
            $query = $query->where(function ($subquery) use ($request) {
                $subquery->where('domain', 'LIKE', '%' . $request->input('keyword') . '%')
                ->orWhere('title', 'LIKE', '%' . $request->input('keyword') . '%')
                ->orWhere('description', 'LIKE', '%' . $request->input('keyword') . '%');
            });
        }

        return $query->paginate();
    }

    public function store(Request $request)
    {
        if (empty($request->user()) || $request->user()->is_admin !== 1)
        {
            return response(null, 403);
        }

        $rules = [
            'domain' => 'required|unique:movies,domain',
            'title' => 'required'
        ];

        $this->validate($request, $rules);

        $movie = Movie::create([
            'domain' => $request->input('domain'),
            'title' => $request->input('title'),
            'description' => $request->input('description')
        ]);

        return response(['resource_id' => $movie->id], 200);
    }

    public function show(Request $request, $id = null)
    {
        $query = Movie::with([
            'videos' => function ($query) use ($request) {
                $query->select(['id', 'movie_id', 'reference', 'type']);

                if (!empty($request->input('t')))
                {
                    $query = $query->where('type', $request->input('t'));
                }
            }
        ])
        ->select(['id', 'title', 'description', 'cover', 'disclaimer', 'domain']);

        if (!empty($id))
        {
            if (empty($request->user()) || $request->user()->is_admin !== 1)
            {
                return response(null, 403);
            }

            $movie = $query->findOrFail($id);
        }
        else
        {
            if (!empty($request->input('d')))
            {
                $query = $query->where('domain', str_replace('www.', '', $request->input('d')));
            }
            $movie = $query->firstOrFail();
        }

        $base = null;
        if ($movie->domain !== 'localhost')
        {
            $base = 'https://api.' . $movie->domain . '/covers/';
        }
        else
        {
            $base = 'http://api.fred.test/covers/';
        }

        return response([
            'id' => $movie->id,
            'title' => $movie->title,
            'description' => $movie->description,
            'cover' => $base . $movie->cover,
            'disclaimer' => $base . $movie->disclaimer,
            'videos' => $movie->videos
        ], 200);
    }

    public function update(Request $request)
    {
        if (empty($request->user()) || $request->user()->is_admin !== 1)
        {
            return response(null, 403);
        }

        $rules = [
            'domain' => 'required|unique:movies,domain',
            'title' => 'required'
        ];

        $this->validate($request, $rules);

        Movie::findOrFail($id)->update([
            'domain' => $request->input('domain'),
            'title' => $request->input('title'),
            'description' => $request->input('description')
        ]);

        return response(null, 204);
    }
}
