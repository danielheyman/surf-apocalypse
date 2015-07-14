<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use Auth;
use Mail;
use Illuminate\Http\Request;

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
        $fields = array_merge($request->all(), ['confirmation_code' => str_random(30), 'human' => true]);

        $user = User::create($fields);

        Mail::send('emails.verify', compact('user'), function ($message) use ($user) {
            $message->to($user->email, $user->name);
            $message->subject('Welcome to bla');
        });

        return redirect()->back()->with('status', 'Thanks for signing up! Please check your email for a confirmation link.');
    }

    public function confirm($confirmation_code)
    {
        $user = User::whereConfirmationCode($confirmation_code)->first();

        if (!$user) {
            \App::abort(404);
        }

        $user->confirmation_code = null;
        $user->save();

        Auth::login($user);

        return redirect('/')->with('message', 'You have successfully verified your account.');
    }

    public function resend()
    {
        return view('auth.resend');
    }

    public function postResend(Request $request)
    {
        $this->validate($request, ['email' => 'required|email']);

        $user = User::whereEmail($request->get('email'))->first(['name', 'email', 'confirmation_code']);

        if (!$user) {
            return redirect()->back()->withErrors(['email' => 'Email not found.']);
        }

        if ($user->confirmation_code) {
            return redirect()->back()->withErrors(['email' => 'Account already confirmed.']);
        }

        Mail::send('emails.verify', compact('user'), function ($message) use ($user) {
            $message->to($user->email, $user->name);
            $message->subject('Welcome to bla');
        });

        return redirect()->back()->with('status', 'Please check your email for a confirmation link.');
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
