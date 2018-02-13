<?php

namespace App\Http\Controllers;

use App\Models\User;
use Auth;
use Dingo\Api\Http\Request;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required|string|min:6|max:20'
        ]);

        $data = $request->only(['email', 'password']);

        $token = Auth::attempt($data);

        if ($token) {
            return ['token' => $token];
        } else {
            return $this->response->errorUnauthorized('账号或密码错误');
        }
    }

    public function register(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|string|min:6|max:20'
        ]);

        // create a user
        $data = $request->only(['email', 'password']);
        $data['password'] = app('hash')->make($data['password']);
        $user = User::create($data);

        // get token
        $token = Auth::guard()->fromUser($user);
        return compact('token');
    }
}
