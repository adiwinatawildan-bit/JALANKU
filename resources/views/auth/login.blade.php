@extends('layouts.app')

@section('title', 'Masuk ke Akun - ' . \App\Models\SystemSetting::appName())

@section('content')
<div class="min-h-[80vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        
        <!-- Header -->
        <div class="text-center space-y-2">
            <div class="w-14 h-14 rounded-2xl bg-navy-900 text-amber-400 flex items-center justify-center mx-auto text-2xl shadow-xl">
                <i class="fa-solid fa-lock"></i>
            </div>
            <h2 class="text-3xl font-extrabold text-navy-900 tracking-tight">Masuk ke Portal</h2>
            <p class="text-xs text-slate-500">Gunakan akun terdaftar Anda untuk melaporkan atau mengelola pengaduan jalan.</p>
        </div>

        <!-- Card Form -->
        <div class="bg-white p-8 rounded-3xl border border-slate-200 shadow-xl space-y-6">
            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf

                <!-- Email -->
                <div class="space-y-1">
                    <label for="email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Alamat Email</label>
                    <div class="relative">
                        <i class="fa-solid fa-envelope absolute left-3.5 top-3 text-slate-400 text-sm"></i>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus placeholder="nama@email.com" class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-amber-500 focus:outline-none">
                    </div>
                </div>

                <!-- Password -->
                <div class="space-y-1">
                    <div class="flex justify-between items-center">
                        <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Password</label>
                    </div>
                    <div class="relative">
                        <i class="fa-solid fa-key absolute left-3.5 top-3 text-slate-400 text-sm"></i>
                        <input id="password" type="password" name="password" required placeholder="••••••••" class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-semibold focus:ring-2 focus:ring-amber-500 focus:outline-none">
                    </div>
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between text-xs pt-1">
                    <label class="flex items-center space-x-2 text-slate-600">
                        <input type="checkbox" name="remember" class="rounded border-slate-300 text-amber-600 focus:ring-amber-500">
                        <span>Ingat saya di perangkat ini</span>
                    </label>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="w-full py-3 rounded-xl bg-navy-900 hover:bg-amber-600 text-white font-bold text-sm shadow-lg shadow-navy-900/20 transition">
                    <i class="fa-solid fa-right-to-bracket mr-2"></i> Masuk Sekarang
                </button>
            </form>

            <div class="border-t border-slate-100 pt-4 text-center">
                <p class="text-xs text-slate-500">
                    Belum punya akun masyarakat?
                    <a href="{{ route('register') }}" class="font-bold text-amber-600 hover:underline">Daftar Akun Baru</a>
                </p>
            </div>

        </div>

    </div>
</div>
@endsection
