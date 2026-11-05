<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\Wishlist;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function showLogin(Request $request)
    {
        $this->rememberNext($request);

        return view('auth.login');
    }

    public function login(Request $request, Wishlist $wishlist)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ], ['email.*' => 'Please enter a valid email address.', 'password.required' => 'Please enter your password.']);

        $credentials['email'] = strtolower(trim($credentials['email']));
        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withInput($request->only('email', 'remember'))
                ->with('formError', 'That email and password don’t match an account.');
        }

        $request->session()->regenerate();
        $wishlist->mergeGuest();

        return redirect()->intended(route('account.dashboard'))
            ->with('toast', ['message' => 'Welcome back, '.str(Auth::user()->name)->before(' ').'!', 'type' => 'success']);
    }

    public function showRegister(Request $request)
    {
        $this->rememberNext($request);

        return view('auth.register');
    }

    public function register(Request $request, Wishlist $wishlist)
    {
        $request->merge(['email' => strtolower(trim((string) $request->input('email'))), 'phone' => normalize_phone($request->input('phone'))]);
        $data = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:120'],
            'email' => ['required', 'email', 'max:190', 'unique:users,email'],
            'phone' => ['required', 'regex:/^01[3-9]\d{8}$/'],
            'password' => ['required', 'confirmed', Password::min(8)],
            'terms' => ['accepted'],
        ], [
            'name.*' => 'Please enter your full name.',
            'email.unique' => 'An account with this email already exists. Try logging in.',
            'email.*' => 'Please enter a valid email address.',
            'phone.*' => 'Enter a valid Bangladeshi mobile number (e.g. 01712345678).',
            'password.confirmed' => 'The passwords don’t match.',
            'password.*' => 'Use at least 8 characters.',
            'terms.accepted' => 'Please accept the terms to continue.',
        ]);

        $user = User::create($data);
        event(new Registered($user));
        Auth::login($user);
        $request->session()->regenerate();
        $wishlist->mergeGuest();

        return redirect()->intended(route('account.dashboard'))
            ->with('toast', ['message' => 'Welcome to Ashfi Collection!', 'type' => 'success']);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('toast', ['message' => 'You have been logged out.', 'type' => 'success']);
    }

    /** ?next=/checkout – only same-site paths are accepted. */
    private function rememberNext(Request $request): void
    {
        $next = (string) $request->query('next', '');
        if ($next !== '' && str_starts_with($next, '/') && ! str_starts_with($next, '//') && ! str_contains($next, '\\')) {
            $request->session()->put('url.intended', url($next));
        }
    }
}
