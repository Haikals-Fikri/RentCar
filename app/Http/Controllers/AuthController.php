<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // Tampilkan halaman register & login
    public function showRegisterUser()  { return view('register-user'); }
    public function showRegisterOwner() { return view('register-owner'); }
    public function showLoginUser()     { return view('login-user'); }
    public function showLoginOwner()    { return view('login-owner'); }
    public function showLoginAdmin()    { return view('login-admin'); }

    // Proses Registrasi User
    public function registerUser(Request $request)
    {
        $this->validateRegister($request);

        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => 'user'
        ]);

        return redirect('/login-user')->with('success', 'Registrasi berhasil. Silakan login.');
    }

    // Proses Registrasi Owner
    public function registerOwner(Request $request)
    {
        $this->validateRegister($request);

        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => 'owner'
        ]);

        return redirect('/login-owner')->with('success', 'Registrasi berhasil. Silakan login.');
    }

    // Proses Login User
    public function loginUser(Request $request)
    {
        $this->validateLogin($request);

        if (Auth::attempt($request->only('email', 'password'))) {
            if (Auth::user()->role === 'user') {
                return redirect('/user/dashboard');
            }
            Auth::logout();
            return redirect('/login-user')->withErrors('Akun Anda bukan User.');
        }

        return redirect('/login-user')->withErrors('Email atau password salah.');
    }

    // Proses Login Owner
    public function loginOwner(Request $request)
    {
        $this->validateLogin($request);

        if (Auth::attempt($request->only('email', 'password'))) {
            if (Auth::user()->role === 'owner') {
                // Simpan owner_id ke session
                session(['owner_id' => Auth::id()]);
                return redirect('/owner/dashboard');
            }
            Auth::logout();
            return redirect('/login-owner')->withErrors('Akun Anda bukan Owner.');
        }

        return redirect('/login-owner')->withErrors('Email atau password salah.');
    }

    // Proses Login Admin
    public function loginAdmin(Request $request)
    {
        $this->validateLogin($request);

        if (Auth::attempt($request->only('email', 'password'))) {
            if (Auth::user()->role === 'admin') {
                return redirect('/admin/dashboard');
            }
            Auth::logout();
            return redirect('/login-admin')->withErrors('Akun Anda bukan Admin.');
        }

        return redirect('/login-admin')->withErrors('Email atau password salah.');
    }

    // Logout
    public function logout()
    {
        Auth::logout();
        session()->flush(); // hapus semua session
        return redirect('/')->with('success', 'Berhasil logout.');
    }

    // Validasi register
    private function validateRegister(Request $request)
    {
        $request->validate([
            'name'     => 'required',
            'email'    => 'required|email|unique:users',
            'password' => 'required|min:8'
        ]);
    }

    // Validasi login
    private function validateLogin(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required'
        ]);
    }
}
