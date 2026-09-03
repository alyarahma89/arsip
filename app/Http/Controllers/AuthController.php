<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class AuthController extends Controller
{
    // ==========================================
    // 1. TAMPILKAN FORM LOGIN (DENGAN MATH CAPTCHA)
    // ==========================================
    public function showLogin()
    {
        // Kita buat angka acak di sini
        $n1 = rand(1, 9);
        $n2 = rand(1, 9);
        $total = $n1 + $n2;

        // Kita buat kunci jawaban rahasia yang di-enkripsi
        $hash = Hash::make($total);

        // Kirim variabel ke view auth.login
        return view('auth.login', compact('n1', 'n2', 'hash'));
    }

    // ==========================================
    // 2. PROSES LOGIN (EMAIL + PASSWORD + MATH CAPTCHA)
    // ==========================================
    public function loginProcess(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'email'        => 'required|email',
            'password'     => 'required|string',
            'captcha'      => 'required|numeric',
            'captcha_hash' => 'required'
        ], [
            'email.required'    => 'Email wajib diisi.',
            'password.required' => 'Password wajib diisi.',
            'captcha.required'  => 'Hasil pertambahan wajib diisi.',
            'captcha.numeric'   => 'Isi dengan angka saja.',
        ]);

        // 2. Cek Hitungan Captcha (Hash Check)
        if (!Hash::check($request->captcha, $request->captcha_hash)) {
            return back()->withErrors([
                'captcha' => 'Waduh, hasil hitungan salah! Coba hitung lagi yang teliti ya Min.'
            ])->withInput();
        }

        // 3. Cek Login (Email & Password)
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $request->session()->regenerate();
            return redirect()->route('dashboard');
        }

        // 4. Jika Gagal (Email/Password Salah)
        return back()->withErrors([
            'login_error' => 'Email atau Password yang Anda masukkan salah.',
        ])->withInput($request->only('email'));
    }

    // ==========================================
    // 3. REGISTER (PEGAWAI)
    // ==========================================
    public function showRegister()
    {
        return view('auth.register');
    }

    public function processRegister(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'nip' => 'required|numeric',
            'jabatan' => 'required|string',
            'password' => 'required|min:6',
            'kode_rahasia' => 'required'
        ]);

        if ($request->kode_rahasia != '20262026') {
            return back()->with('error', 'Kode Registrasi Salah!');
        }

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'nip' => $request->nip,
            'jabatan' => $request->jabatan,
            'role' => 'pegawai',
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('login')->with('success', 'Registrasi Sukses! Silakan Login.');
    }

    public function updateProfile(Request $request)
{
    $user = \App\Models\User::find(\Illuminate\Support\Facades\Auth::id());

    $request->validate([
        'name'     => 'required|string|max:255',
        'email'    => 'required|email|unique:users,email,' . $user->id,
        'password' => 'nullable|min:8|confirmed', // 'confirmed' artinya harus sama dengan field password_confirmation
    ]);

    $data = [
        'name'  => $request->name,
        'email' => $request->email,
        // tambahkan field lain jika ada (nip, jabatan, dll)
    ];

    // JIKA PASSWORD DIISI, MAKA ENKRIPSI DAN UPDATE
    if ($request->filled('password')) {
        $data['password'] = \Illuminate\Support\Facades\Hash::make($request->password);
    }

    $user->update($data);

    return back()->with('success', 'Profil dan Password berhasil diperbarui! Sekarang lebih aman, Min. 🛡️');
}

    // ==========================================
    // 4. LOGOUT
    // ==========================================
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda berhasil logout! Sampai jumpa lagi 👋');
    }
}
