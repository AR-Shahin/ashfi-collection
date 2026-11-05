<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;

class PasswordResetController extends Controller
{
    public function showForgot()
    {
        return view('auth.forgot-password');
    }

    public function sendLink(Request $request)
    {
        $request->validate(['email' => ['required', 'email']], ['email.*' => 'Please enter a valid email address.']);
        Password::sendResetLink(['email' => strtolower(trim($request->input('email')))]);

        // Same answer whether or not the account exists, so emails can't be probed.
        return back()->with('linkSent', strtolower(trim($request->input('email'))));
    }

    public function showReset(Request $request, string $token)
    {
        return view('auth.reset-password', ['token' => $token, 'email' => $request->query('email')]);
    }

    public function reset(Request $request)
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', PasswordRule::min(8)],
        ], ['password.confirmed' => 'The passwords don’t match.', 'password.*' => 'Use at least 8 characters.']);

        $status = Password::reset($request->only('email', 'password', 'password_confirmation', 'token'), function (User $user, string $password) {
            $user->forceFill(['password' => $password, 'remember_token' => Str::random(60)])->save();
            event(new PasswordReset($user));
        });

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('toast', ['message' => 'Password updated. Please log in.', 'type' => 'success'])
            : back()->withInput($request->only('email'))->with('formError', 'This reset link is invalid or has expired. Please request a new one.');
    }
}
