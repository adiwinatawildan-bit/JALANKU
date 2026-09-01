<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user());
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();

            if (!$user->is_active) {
                Auth::logout();
                return back()->withErrors(['email' => 'Akun Anda sedang dinonaktifkan oleh administrator.'])->withInput();
            }

            $request->session()->regenerate();

            AuditLog::record(
                activity: 'User Login',
                targetType: 'User',
                targetId: $user->id,
                description: "Pengguna {$user->name} ({$user->role->display_name}) berhasil login.",
                userId: $user->id
            );

            return $this->redirectBasedOnRole($user)->with('success', "Selamat datang kembali, {$user->name}!");
        }

        return back()->withErrors([
            'email' => 'Email atau password yang Anda masukkan tidak sesuai.',
        ])->onlyInput('email');
    }

    public function showRegister()
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user());
        }
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone' => ['required', 'string', 'max:20'],
            'password' => ['required', 'confirmed', Password::min(6)],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'password' => Hash::make($validated['password']),
            'role_id' => 1, // Default to masyarakat
            'is_active' => true,
        ]);

        Auth::login($user);

        AuditLog::record(
            activity: 'Registrasi Akun Masyarakat',
            targetType: 'User',
            targetId: $user->id,
            description: "Pengguna masyarakat baru {$user->name} terdaftar.",
            userId: $user->id
        );

        return redirect()->route('masyarakat.dashboard')->with('success', 'Registrasi berhasil! Selamat datang di Portal JALAN KU.');
    }

    public function logout(Request $request)
    {
        if (Auth::check()) {
            AuditLog::record(
                activity: 'User Logout',
                targetType: 'User',
                targetId: Auth::id(),
                description: 'Pengguna melakukan logout.',
                userId: Auth::id()
            );
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'Anda telah berhasil logout.');
    }

    public function profile()
    {
        $user = Auth::user();
        return view('auth.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,svg', 'max:2048'],
            'password' => ['nullable', 'confirmed', Password::min(6)],
        ]);

        $user->name = $validated['name'];
        if (isset($validated['phone'])) {
            $user->phone = $validated['phone'];
        }

        if ($request->hasFile('avatar')) {
            if ($user->avatar_url && Storage::disk('public')->exists($user->avatar_url)) {
                Storage::disk('public')->delete($user->avatar_url);
            }
            $user->avatar_url = $request->file('avatar')->store('avatars', 'public');
        } elseif ($request->boolean('remove_avatar')) {
            if ($user->avatar_url && Storage::disk('public')->exists($user->avatar_url)) {
                Storage::disk('public')->delete($user->avatar_url);
            }
            $user->avatar_url = null;
        }

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }
        $user->save();

        AuditLog::record(
            activity: 'Update Profil',
            targetType: 'User',
            targetId: $user->id,
            description: 'Pengguna memperbarui informasi profil & foto profil.',
            userId: $user->id
        );

        return back()->with('success', 'Profil dan foto Anda berhasil diperbarui.');
    }

    public function redirectBasedOnRole(User $user)
    {
        return match ($user->role?->name) {
            'super_admin' => redirect()->route('superadmin.dashboard'),
            'admin' => redirect()->route('admin.dashboard'),
            'opd' => redirect()->route('opd.dashboard'),
            default => redirect()->route('masyarakat.dashboard'),
        };
    }
}
