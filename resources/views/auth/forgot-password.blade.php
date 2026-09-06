<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Lupa Kata Sandi - {{ \App\Models\SystemSetting::appName() }}</title>

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        navy: {
                            800: '#1e293b',
                            900: '#0f172a',
                            950: '#090d16',
                        },
                        amber: {
                            400: '#fbbf24',
                            500: '#f59e0b',
                            600: '#d97706',
                            700: '#b45309',
                        }
                    },
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>
</head>
<body class="min-h-screen bg-navy-950 text-slate-800 antialiased relative selection:bg-amber-500 selection:text-navy-950 flex items-center justify-center p-4 sm:p-6 md:p-8">

    <!-- FULL SCREEN BACKGROUND IMAGE WITH DEPTH OVERLAYS -->
    <div class="fixed inset-0 z-0 overflow-hidden pointer-events-none">
        <div class="absolute inset-0 bg-cover bg-center scale-105 transition-transform duration-1000" style="background-image: url('{{ asset('images/login-bg.jpg') }}');"></div>
        <div class="absolute inset-0 bg-gradient-to-t from-navy-950 via-navy-950/75 to-navy-950/60 backdrop-blur-[2px]"></div>
        <div class="absolute -top-32 -left-32 w-96 h-96 bg-amber-500/15 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-32 -right-32 w-96 h-96 bg-amber-500/10 rounded-full blur-3xl"></div>
    </div>

    <!-- CENTERED FORGOT PASSWORD FORM CARD -->
    <div class="w-full max-w-md relative z-10 my-auto">
        <div class="bg-white/95 backdrop-blur-xl rounded-3xl p-6 sm:p-9 shadow-2xl border border-white/60 ring-1 ring-black/5 space-y-5">

            <!-- Card Header & Branding -->
            <div class="text-center space-y-3">
                <!-- App Logo -->
                <div class="inline-flex items-center justify-center">
                    <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-2xl overflow-hidden shadow-xl bg-navy-950 p-1 border border-amber-500/40 ring-4 ring-amber-500/15">
                        <img src="{{ asset('images/jalanku-hex-logo.jpg') }}" alt="{{ \App\Models\SystemSetting::appName() }}" class="w-full h-full object-cover rounded-xl">
                    </div>
                </div>

                <div>
                    <span class="text-[11px] font-bold text-amber-600 uppercase tracking-widest block mb-0.5">Pemulihan Akun</span>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-navy-900 tracking-tight">{{ \App\Models\SystemSetting::appName() }}</h1>
                </div>

                <div class="space-y-1">
                    <h2 class="text-lg sm:text-xl font-bold text-slate-800 tracking-tight">
                        Lupa Kata Sandi
                    </h2>
                    <p class="text-xs text-slate-500 leading-relaxed max-w-xs mx-auto">
                        Masukkan email dan nomor HP terdaftar untuk verifikasi dan membuat password baru.
                    </p>
                </div>
            </div>

            <!-- Flash Message Alerts -->
            @if(session('success'))
                <div class="p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center space-x-2">
                    <i class="fa-solid fa-circle-check text-emerald-600 shrink-0"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold space-y-1">
                    <div class="flex items-center space-x-2">
                        <i class="fa-solid fa-circle-exclamation text-rose-600 shrink-0"></i>
                        <span>{{ $errors->first() }}</span>
                    </div>
                </div>
            @endif

            <!-- Reset Form -->
            <form method="POST" action="{{ route('password.update') }}" class="space-y-3.5">
                @csrf

                <!-- Email -->
                <div class="space-y-1">
                    <label for="email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                        Alamat Email Terdaftar <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i class="fa-solid fa-envelope text-sm"></i>
                        </div>
                        <input 
                            id="email" 
                            type="email" 
                            name="email" 
                            value="{{ old('email') }}" 
                            required 
                            autofocus 
                            placeholder="nama@email.com" 
                            class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs sm:text-sm font-medium text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:bg-white focus:border-amber-500 transition shadow-sm"
                        >
                    </div>
                </div>

                <!-- Phone -->
                <div class="space-y-1">
                    <label for="phone" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                        Nomor HP Terdaftar <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i class="fa-solid fa-phone text-sm"></i>
                        </div>
                        <input 
                            id="phone" 
                            type="text" 
                            name="phone" 
                            value="{{ old('phone') }}" 
                            required 
                            placeholder="Contoh: 081234567890" 
                            class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs sm:text-sm font-medium text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:bg-white focus:border-amber-500 transition shadow-sm"
                        >
                    </div>
                </div>

                <!-- Password Baru -->
                <div class="space-y-1 pt-1 border-t border-slate-100">
                    <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                        Password Baru <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i class="fa-solid fa-lock text-sm"></i>
                        </div>
                        <input 
                            id="password" 
                            type="password" 
                            name="password" 
                            required 
                            placeholder="Minimal 6 karakter" 
                            class="w-full pl-10 pr-11 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs sm:text-sm font-medium text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:bg-white focus:border-amber-500 transition shadow-sm"
                        >
                        <button 
                            type="button" 
                            onclick="toggleField('password', 'eyePassword')" 
                            class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-amber-600 transition focus:outline-none"
                            tabindex="-1"
                        >
                            <i class="fa-regular fa-eye-slash text-sm" id="eyePassword"></i>
                        </button>
                    </div>
                </div>

                <!-- Konfirmasi Password Baru -->
                <div class="space-y-1">
                    <label for="password_confirmation" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                        Konfirmasi Password Baru <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i class="fa-solid fa-shield-halved text-sm"></i>
                        </div>
                        <input 
                            id="password_confirmation" 
                            type="password" 
                            name="password_confirmation" 
                            required 
                            placeholder="Ulangi password baru" 
                            class="w-full pl-10 pr-11 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs sm:text-sm font-medium text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:bg-white focus:border-amber-500 transition shadow-sm"
                        >
                        <button 
                            type="button" 
                            onclick="toggleField('password_confirmation', 'eyeConfirm')" 
                            class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-amber-600 transition focus:outline-none"
                            tabindex="-1"
                        >
                            <i class="fa-regular fa-eye-slash text-sm" id="eyeConfirm"></i>
                        </button>
                    </div>
                </div>

                <!-- Submit Button -->
                <button 
                    type="submit" 
                    class="w-full py-3.5 px-4 bg-gradient-to-r from-amber-500 via-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-navy-950 font-extrabold text-sm rounded-xl shadow-lg shadow-amber-500/25 transition duration-150 ease-in-out flex items-center justify-center space-x-2 group mt-2"
                >
                    <i class="fa-solid fa-rotate text-xs group-hover:rotate-180 transition-transform duration-300"></i>
                    <span>Perbarui Kata Sandi</span>
                </button>
            </form>

            <!-- Back to Login Link -->
            <div class="border-t border-slate-200/80 pt-3 text-center">
                <p class="text-xs text-slate-500">
                    Sudah ingat kata sandi akun?
                    <a href="{{ route('login') }}" class="font-bold text-amber-600 hover:text-amber-700 hover:underline inline-flex items-center space-x-1">
                        <span>Kembali ke Login</span>
                    </a>
                </p>
            </div>

        </div>
    </div>

    <!-- Toggle Password Script -->
    <script>
        function toggleField(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);
            if (!input || !icon) return;

            const isPassword = input.getAttribute('type') === 'password';
            input.setAttribute('type', isPassword ? 'text' : 'password');

            if (isPassword) {
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            } else {
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            }
        }
    </script>
</body>
</html>
