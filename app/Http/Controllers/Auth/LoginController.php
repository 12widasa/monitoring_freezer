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

        if (Auth::attempt([$fieldType => $credentials['identity'], 'password' => $credentials['password']])) {
            $request->session()->regenerate();

            return match (Auth::user()->role) {
                'admin' => redirect()->intended('/admin/dashboard'),
                'technician' => redirect()->intended('/technician/repairs'),
                'customer' => redirect()->intended('/customer/monitoring'),
                default => redirect()->intended('/login'),
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
