<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use Auth;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest', ['except' => 'getLogout']);
    }

    public function getRegister()
    {
        return view('auth.register');
    }

    public function postRegister(RegisterRequest $request)
    {
        $user = User::create($request->all());

        Auth::login($user);

        return redirect('/');
    }

    public function getLogin()
    {
        return view('auth.login');
    }

    public function postLogin(LoginRequest $request)
    {
        Auth::attempt($request->only('email', 'password'), $request->has('remember'));

        return redirect()->intended('/');
    }

    public function getLogout()
    {
        Auth::logout();

        return redirect('/');
    }
}
