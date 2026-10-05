<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLoginForm(): View
    {
        return view('pages.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $username = trim((string) $credentials['username']);
        $password = (string) $credentials['password'];

        $loginSucceeded = Auth::attempt([
            'name' => $username,
            'password' => $password,
        ], $request->boolean('remember'));

        if (! $loginSucceeded) {
            $fallbackUser = \App\Models\User::where('email', $username)->first();
            if ($fallbackUser && password_verify($password, $fallbackUser->password)) {
                Auth::login($fallbackUser, $request->boolean('remember'));
                $loginSucceeded = true;
            }
        }

        if ($loginSucceeded) {
            $request->session()->regenerate();

            return redirect()->intended(route('dashboard'));
        }

        return back()
            ->withErrors([
                'username' => 'User atau password salah.',
            ])
            ->onlyInput('username');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
