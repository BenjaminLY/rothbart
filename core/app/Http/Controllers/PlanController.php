<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Plan;

class PlanController extends Controller
{
    // get plans listing
    public function index(Request $request)
    {
        if (!empty($request->input('full')))
        {
            return Plan::get();
        }
        else
        {
            return Plan::paginate();
        }
    }

    // create plan
    public function store(Request $request)
    {
        if ($request->user()->is_admin !== 1)
        {
            return response(null, 403);
        }

        $rules = [
            'title' => 'required|unique:plans,title',
            'price' => 'required',
            'type' => 'required|enum:stream,download,download_stream'
        ];

        $this->validate($request, $rules);

        $data = [
            'title' => $request->input('title'),
            'price' => $request->input('price'),
            'type' => $request->input('type')
        ];

        $plan = Plan::create($data);

        return response(['resource_id' => $plan->id], 201);
    }

    // show plan details
    public function show(Request $request, $id)
    {
        return Plan::findOrFail($id);
    }

    // update plan
    public function update(Request $request, $id)
    {
        if ($request->user()->is_admin !== 1)
        {
            return response(null, 403);
        }

        $rules = [
            'title' => 'required|unique:plans,title',
            'price' => 'required',
            'type' => 'required|enum:stream,download,download_stream'
        ];

        $this->validate($request, $rules);

        $data = [
            'title' => $request->input('title'),
            'price' => $request->input('price'),
            'type' => $request->input('type')
        ];

        Plan::findOrFail($id)->update($data);

        return response(204);
    }
}
