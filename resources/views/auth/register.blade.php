@extends('layouts.app')

@section('title', 'Registrasi Akun Masyarakat - ' . \App\Models\SystemSetting::appName())

@section('content')
<div class="min-h-[80vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        
        <!-- Header -->
        <div class="text-center space-y-2">
            <div class="w-14 h-14 rounded-2xl bg-amber-500 text-navy-950 flex items-center justify-center mx-auto text-2xl shadow-xl">
                <i class="fa-solid fa-user-plus"></i>
            </div>
            <h2 class="text-3xl font-extrabold text-navy-900 tracking-tight">Daftar Akun Masyarakat</h2>
            <p class="text-xs text-slate-500">Daftarkan akun untuk melaporkan dan memantau perbaikan jalan umum secara langsung.</p>
        </div>

        <!-- Card Form -->
        <div class="bg-white p-8 rounded-3xl border border-slate-200 shadow-xl space-y-6">
            <form method="POST" action="{{ route('register') }}" class="space-y-4">
                @csrf

                <!-- Nama Lengkap -->
                <div class="space-y-1">
                    <label for="name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Nama Lengkap</label>
                    <div class="relative">
                        <i class="fa-solid fa-user absolute left-3.5 top-3 text-slate-400 text-sm"></i>
                        <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus placeholder="Nama sesuai identitas" class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-amber-500 focus:outline-none">
                    </div>
                </div>

                <!-- Email -->
                <div class="space-y-1">
                    <label for="email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Alamat Email</label>
                    <div class="relative">
                        <i class="fa-solid fa-envelope absolute left-3.5 top-3 text-slate-400 text-sm"></i>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required placeholder="email@domain.com" class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-amber-500 focus:outline-none">
                    </div>
                </div>

                <!-- Nomor HP / WhatsApp -->
                <div class="space-y-1">
                    <label for="phone" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Nomor HP / WhatsApp</label>
                    <div class="relative">
                        <i class="fa-solid fa-phone absolute left-3.5 top-3 text-slate-400 text-sm"></i>
                        <input id="phone" type="text" name="phone" value="{{ old('phone') }}" required placeholder="08xxxxxxxxxx" class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-amber-500 focus:outline-none">
                    </div>
                </div>

                <!-- Password -->
                <div class="space-y-1">
                    <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Password</label>
                    <div class="relative">
                        <i class="fa-solid fa-lock absolute left-3.5 top-3 text-slate-400 text-sm"></i>
                        <input id="password" type="password" name="password" required placeholder="Minimal 6 karakter" class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-amber-500 focus:outline-none">
                    </div>
                </div>

                <!-- Confirm Password -->
                <div class="space-y-1">
                    <label for="password_confirmation" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Konfirmasi Password</label>
                    <div class="relative">
                        <i class="fa-solid fa-lock absolute left-3.5 top-3 text-slate-400 text-sm"></i>
                        <input id="password_confirmation" type="password" name="password_confirmation" required placeholder="Ulangi password" class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-amber-500 focus:outline-none">
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="w-full py-3 rounded-xl bg-amber-500 hover:bg-amber-600 text-navy-950 font-bold text-sm shadow-lg shadow-amber-500/20 transition">
                    <i class="fa-solid fa-user-check mr-2"></i> Daftar Sebagai Masyarakat
                </button>
            </form>

            <div class="border-t border-slate-100 pt-4 text-center">
                <p class="text-xs text-slate-500">
                    Sudah memiliki akun?
                    <a href="{{ route('login') }}" class="font-bold text-navy-900 hover:underline">Masuk ke Portal</a>
                </p>
            </div>

        </div>

    </div>
</div>
@endsection
