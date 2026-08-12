<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'identity' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $fieldType = filter_var($request->identity, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        // [Pasti] Tangkap nilai checkbox "remember"
        $remember = $request->boolean('remember');

        // [Pasti] Oper variabel $remember sebagai parameter kedua Auth::attempt
        if (Auth::attempt([
            $fieldType => $credentials['identity'],
            'password' => $credentials['password'],
            'is_active' => true,
        ], $remember)) {
            $request->session()->regenerate();

            return match (Auth::user()->role->value) {
                'admin' => redirect()->intended(route('admin.dashboard')),
                'technician' => redirect()->intended(route('technician.dashboard')),
                'customer' => redirect()->intended(route('customer.monitoring')),
                default => redirect()->route('login'),
            };
        }

        return back()->withErrors([
            'identity' => 'Email/Username atau kata sandi tidak cocok.',
        ])->onlyInput('identity');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
