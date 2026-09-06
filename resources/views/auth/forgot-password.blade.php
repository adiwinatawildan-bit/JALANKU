@extends('layouts.app')

@section('title', 'Lupa Kata Sandi - ' . \App\Models\SystemSetting::appName())

@section('content')
<div class="min-h-[80vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        
        <!-- Header -->
        <div class="text-center space-y-2">
            <div class="w-14 h-14 rounded-2xl bg-navy-900 text-amber-400 flex items-center justify-center mx-auto text-2xl shadow-xl">
                <i class="fa-solid fa-key"></i>
            </div>
            <h2 class="text-3xl font-extrabold text-navy-900 tracking-tight">Lupa Kata Sandi</h2>
            <p class="text-xs text-slate-500">Masukkan email dan nomor HP terdaftar untuk memverifikasi akun dan membuat password baru.</p>
        </div>

        <!-- Card Form -->
        <div class="bg-white p-8 rounded-3xl border border-slate-200 shadow-xl space-y-6">
            <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
                @csrf

                <!-- Email -->
                <div class="space-y-1">
                    <label for="email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Alamat Email Terdaftar <span class="text-rose-500">*</span></label>
                    <div class="relative">
                        <i class="fa-solid fa-envelope absolute left-3.5 top-3 text-slate-400 text-sm"></i>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus placeholder="nama@email.com" class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border @error('email') border-rose-500 @else border-slate-300 @enderror rounded-xl text-xs font-semibold focus:ring-2 focus:ring-amber-500 focus:outline-none">
                    </div>
                    @error('email')
                        <p class="text-xs text-rose-600 font-bold mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Phone -->
                <div class="space-y-1">
                    <label for="phone" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Nomor HP Terdaftar <span class="text-rose-500">*</span></label>
                    <div class="relative">
                        <i class="fa-solid fa-phone absolute left-3.5 top-3 text-slate-400 text-sm"></i>
                        <input id="phone" type="text" name="phone" value="{{ old('phone') }}" required placeholder="Contoh: 081234567890" class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border @error('phone') border-rose-500 @else border-slate-300 @enderror rounded-xl text-xs font-semibold focus:ring-2 focus:ring-amber-500 focus:outline-none">
                    </div>
                    @error('phone')
                        <p class="text-xs text-rose-600 font-bold mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- New Password -->
                <div class="space-y-1 pt-2 border-t border-slate-100">
                    <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Password Baru <span class="text-rose-500">*</span></label>
                    <div class="relative">
                        <i class="fa-solid fa-lock absolute left-3.5 top-3 text-slate-400 text-sm"></i>
                        <input id="password" type="password" name="password" required placeholder="Minimal 6 karakter" class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border @error('password') border-rose-500 @else border-slate-300 @enderror rounded-xl text-xs font-semibold focus:ring-2 focus:ring-amber-500 focus:outline-none">
                    </div>
                    @error('password')
                        <p class="text-xs text-rose-600 font-bold mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Confirm Password -->
                <div class="space-y-1">
                    <label for="password_confirmation" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Konfirmasi Password Baru <span class="text-rose-500">*</span></label>
                    <div class="relative">
                        <i class="fa-solid fa-shield-halved absolute left-3.5 top-3 text-slate-400 text-sm"></i>
                        <input id="password_confirmation" type="password" name="password_confirmation" required placeholder="Ulangi password baru" class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-amber-500 focus:outline-none">
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="w-full py-3 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-navy-950 font-extrabold text-sm shadow-xl shadow-amber-500/20 transition mt-2">
                    <i class="fa-solid fa-rotate mr-2"></i> Perbarui Kata Sandi
                </button>
            </form>

            <div class="border-t border-slate-100 pt-4 text-center">
                <p class="text-xs text-slate-500">
                    Sudah ingat kata sandi akun?
                    <a href="{{ route('login') }}" class="font-bold text-amber-600 hover:underline">Kembali ke Login</a>
                </p>
            </div>

        </div>

    </div>
</div>
@endsection
