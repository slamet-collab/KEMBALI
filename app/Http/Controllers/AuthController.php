<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return $this->redirectUserByRole(Auth::user());
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ], [
            'login.required' => 'NIM / NIP / Email wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        $loginInput = trim($request->input('login'));

        // Check if login input is email or identity_no (NIM/NIP)
        $user = User::where('email', $loginInput)
            ->orWhere('identity_no', $loginInput)
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()->withInput($request->only('login'))
                ->withErrors(['login' => 'NIM/NIP/Email atau password yang Anda masukkan salah.']);
        }

        if ($user->status !== 'active') {
            return back()->withErrors(['login' => 'Akun Anda sedang dinonaktifkan. Silakan hubungi administrator.']);
        }

        Auth::login($user, $request->has('remember'));
        $request->session()->regenerate();

        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'LOGIN',
            'description' => "Pengguna {$user->name} ({$user->role?->name}) berhasil login ke sistem.",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return $this->redirectUserByRole($user)->with('success', "Selamat datang kembali, {$user->name}!");
    }

    public function showRegisterForm()
    {
        if (Auth::check()) {
            return $this->redirectUserByRole(Auth::user());
        }

        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'identity_no' => ['required', 'string', 'max:50', 'regex:/^[0-9]+$/', 'unique:users,identity_no'],
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'terms' => 'accepted',
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'identity_no.required' => 'NIM / NIP / ID Kampus wajib diisi.',
            'identity_no.regex' => 'NIM / NIP / ID Kampus hanya boleh berisi angka.',
            'identity_no.unique' => 'NIM / NIP ini sudah terdaftar dalam sistem.',
            'email.required' => 'Email kampus wajib diisi.',
            'email.unique' => 'Email ini sudah terdaftar dalam sistem.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'terms.accepted' => 'Anda harus menyetujui Syarat & Ketentuan KEMBALI.',
        ]);

        $role = Role::where('code', 'mahasiswa')->firstOrFail();

        $user = User::create([
            'name' => $request->name,
            'identity_no' => $request->identity_no,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $role->id,
            'status' => 'active',
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'REGISTER',
            'description' => "Pengguna baru {$user->name} mendaftar sebagai {$role->name}.",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return $this->redirectUserByRole($user)->with('success', 'Akun KEMBALI Anda berhasil dibuat!');
    }

    public function logout(Request $request)
    {
        if (Auth::check()) {
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'LOGOUT',
                'description' => "Pengguna " . Auth::user()->name . " telah logout.",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('landing')->with('info', 'Anda telah berhasil keluar dari akun.');
    }

    public function redirectUserByRole($user)
    {
        return match ($user->role?->code) {
            'mahasiswa' => redirect()->route('dashboard.mahasiswa'),
            'dosen' => redirect()->route('dashboard.dosen'),
            'staf_barang' => redirect()->route('dashboard.staf'),
            'admin' => redirect()->route('dashboard.admin'),
            'super_admin' => redirect()->route('dashboard.superadmin'),
            default => redirect()->route('landing'),
        };
    }
}
