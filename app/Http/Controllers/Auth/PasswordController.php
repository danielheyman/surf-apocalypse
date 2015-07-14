<?php

namespace app\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class PasswordController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function getEmail()
    {
        return view('auth.password');
    }

    public function postEmail(Request $request)
    {
        $this->validate($request, ['email' => 'required|email']);

        $response = Password::sendResetLink($request->only('email'), function ($message) {
            $message->subject('Your Password Reset Link');
        });

        switch ($response) {
            case Password::RESET_LINK_SENT:
                return redirect()->back()->with('status', trans($response));

            case Password::INVALID_USER:
                return redirect()->back()->withErrors(['email' => trans($response)]);
        }
    }

    public function getReset($token = null)
    {
        return view('auth.reset')->with('token', $token);
    }

    public function postReset(Request $request)
    {
        $this->validate($request, [
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed',
        ]);

        $credentials = $request->only(
            'email', 'password', 'password_confirmation', 'token'
        );

        $response = Password::reset($credentials, function ($user, $password) {
            $user->password = $password;
            $user->save();
            Auth::login($user);
        });

        switch ($response) {
            case Password::PASSWORD_RESET:
                return redirect('/');

            default:
                return redirect()->back()
                            ->withInput($request->only('email'))
                            ->withErrors(['email' => trans($response)]);
        }
    }
}
