<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) return redirect()->route('dashboard');
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            $user = Auth::user();
            if (! $user->is_active) {
                Auth::logout();
                return back()->withErrors(['email' => 'Akun tidak aktif. Hubungi Admin.']);
            }
            if ($user->isSuperAdmin()) {
                return redirect()->route('dashboard');
            }
            if ($user->isSupervisor()) {
                return redirect()->route('reports.index');
            }
            if ($user->isOperator()) {
                return redirect()->route('pos.index');
            }
            return redirect()->route('dashboard');
        }

        // --- DEBUG START ---
        $user = \App\Models\User::where('email', $credentials['email'])->first();
        $debugInfo = "Login Failed. Email: {$credentials['email']}. User exists: " . ($user ? 'Yes' : 'No');
        if ($user) {
            $debugInfo .= ". Hash match: " . (\Illuminate\Support\Facades\Hash::check($credentials['password'], $user->password) ? 'Yes' : 'No');
        }
        // --- DEBUG END ---

        return back()->withErrors([
            'email' => $debugInfo, // Show debug info instead of default error
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
