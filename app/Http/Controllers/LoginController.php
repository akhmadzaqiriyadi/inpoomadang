<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    /**
     * Menampilkan halaman login.
     */
    public function index()
    {
        return view('login'); // Pastikan file view login tersedia
    }

    /**
     * Mengautentikasi pengguna.
     */
    public function authenticate(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
        ]);

        if ($validator->fails()) {
            return redirect()->route('login.index')
                ->withInput()
                ->withErrors($validator);
        }

        Log::info('Login attempt', ['email' => $request->email]);

        // Cek kredensial pengguna
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            Log::info('Login successful', ['email' => $user->email]);

            // Redirect berdasarkan role
            switch ($user->role) {
                case 'admin':
                    return redirect()->route('dashboard.owner'); // Dashboard admin
                case 'pelanggan':
                    return redirect()->route('dashboard.customer'); // Dashboard pelanggan
                default:
                    Log::error('Unknown role for user', ['email' => $user->email, 'role' => $user->role]);
                    abort(403, 'Role tidak dikenali.');
            }
        }

        // Jika login gagal
        Log::warning('Login failed', ['email' => $request->email]);
        return redirect()->back()->withErrors(['login_error' => 'Email atau password salah']);
    }

    /**
     * Logout pengguna.
     */
    public function logout()
    {
        Auth::logout();
        return redirect()->route('login')->with('success', 'Anda telah berhasil logout.');
    }
}
