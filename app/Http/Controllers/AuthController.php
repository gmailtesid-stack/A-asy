<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        // Langsung buang ke dashboard kalau sudah login
        if (Auth::check()) return redirect()->route('dashboard');
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        // "Remember me" dimatikan dulu buat demo biar session nggak ribet di Vercel
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            $user = Auth::user();

            if (! $user->is_active) {
                Auth::logout();
                return back()->withErrors(['email' => 'Akun tidak aktif. Hubungi Admin.']);
            }

            // Langsung arahkan berdasarkan role tanpa tambahan query berat
            $role = $user->role;
            if (in_array($role, ['manager', 'supervisor'])) {
                return redirect()->route('reports.index');
            }
            
            if (in_array($role, ['cashier', 'operator'])) {
                return redirect()->route('pos.index');
            }
            
            return redirect()->route('dashboard');
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
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