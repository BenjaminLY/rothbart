<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\User;

class UserController extends Controller
{
    // get user listing
    public function index(Request $request)
    {
        if ($request->user()->is_admin !== 1)
        {
            return response(null, 403);
        }

        return User::paginate();
    }

    // create user
    public function store(Request $request)
    {
        $rules = [
            'email' => 'email|required|unique:users,email',
            'password' => 'required|min:6'
        ];

        if (!empty($request->input('store_name')))
        {
            $rules['store_name'] = 'required|unique:merchants,name';
        }

        $this->validate($request, $rules);

        $data = [
            'username' => $request->input('email'),
            'email' => $request->input('email'),
            'password' => app('hash')->make($request->input('password'))
        ];

        if (!empty($request->user()) && $request->user()->is_admin !== 1)
        {
            $data['is_admin'] = $request->input('is_admin');
        }

        $user = User::create($data);

        return response(['resource_id' => $user->id], 201);
    }

    // show user details
    public function show(Request $request, $id)
    {
        if ($request->user()->is_admin !== 1 && $request->user()->id !== $id)
        {
            return response(null, 403);
        }

        return User::findOrFail($id);
    }

    // update user
    public function update(Request $request, $id)
    {
        if ($request->user()->is_admin !== 1 && $request->user()->id !== $id)
        {
            return response(null, 403);
        }

        $rules = [
            'email' => 'email|required|unique:users,email',
            'password' => 'required|min:6'
        ];

        if (!empty($request->input('store_name')))
        {
            $rules['store_name'] = 'required|unique:merchants,name';
        }

        $this->validate($request, $rules);

        $data = [
            'username' => $request->input('email'),
            'email' => $request->input('email')
        ];

        if (!empty($password))
        {
            $data['password'] = app('hash')->make($request->input('password'));
        }

        if ($request->user()->is_admin !== 1)
        {
            $data['is_admin'] = $request->input('is_admin');
        }

        User::find($id)->update($data);

        return response(204);
    }
}
