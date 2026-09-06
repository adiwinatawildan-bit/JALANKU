<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Masuk - {{ \App\Models\SystemSetting::appName() }}</title>

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

    <!-- CENTERED LOGIN FORM CARD -->
    <div class="w-full max-w-md relative z-10 my-auto">
        <div class="bg-white/95 backdrop-blur-xl rounded-3xl p-6 sm:p-9 shadow-2xl border border-white/60 ring-1 ring-black/5 space-y-6">

            <!-- Card Header & Branding -->
            <div class="text-center space-y-3">
                <!-- App Logo -->
                <div class="inline-flex items-center justify-center">
                    <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-2xl overflow-hidden shadow-xl bg-navy-950 p-1 border border-amber-500/40 ring-4 ring-amber-500/15">
                        <img src="{{ asset('images/jalanku-hex-logo.jpg') }}" alt="{{ \App\Models\SystemSetting::appName() }}" class="w-full h-full object-cover rounded-xl">
                    </div>
                </div>

                <div>
                    <span class="text-[11px] font-bold text-amber-600 uppercase tracking-widest block mb-0.5">Portal Pelayanan Publik</span>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-navy-900 tracking-tight">{{ \App\Models\SystemSetting::appName() }}</h1>
                </div>

                <div class="space-y-1">
                    <h2 class="text-lg sm:text-xl font-bold text-slate-800 tracking-tight">
                        Selamat Datang Kembali
                    </h2>
                    <p class="text-xs text-slate-500 leading-relaxed max-w-xs mx-auto">
                        Silakan masuk ke akun Anda untuk melanjutkan ke sistem pelaporan.
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

            @if(session('status'))
                <div class="p-3.5 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 text-xs font-semibold flex items-center space-x-2">
                    <i class="fa-solid fa-circle-info text-amber-600 shrink-0"></i>
                    <span>{{ session('status') }}</span>
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

            <!-- Login Form -->
            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf

                <!-- Email atau Username -->
                <div class="space-y-1.5">
                    <label for="email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Email atau Username</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i class="fa-regular fa-user text-sm"></i>
                        </div>
                        <input 
                            id="email" 
                            type="text" 
                            name="email" 
                            value="{{ old('email') }}" 
                            required 
                            autofocus 
                            placeholder="Email atau Username" 
                            class="w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-300 rounded-xl text-xs sm:text-sm font-medium text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:bg-white focus:border-amber-500 transition shadow-sm"
                        >
                    </div>
                </div>

                <!-- Kata Sandi -->
                <div class="space-y-1.5">
                    <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Kata Sandi</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i class="fa-solid fa-lock text-sm"></i>
                        </div>
                        <input 
                            id="password" 
                            type="password" 
                            name="password" 
                            required 
                            placeholder="Kata Sandi" 
                            class="w-full pl-10 pr-11 py-3 bg-slate-50 border border-slate-300 rounded-xl text-xs sm:text-sm font-medium text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:bg-white focus:border-amber-500 transition shadow-sm"
                        >
                        <button 
                            type="button" 
                            id="togglePassword" 
                            class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-amber-600 transition focus:outline-none"
                            tabindex="-1"
                        >
                            <i class="fa-regular fa-eye-slash text-sm" id="eyeIcon"></i>
                        </button>
                    </div>

                    <!-- Lupa Sandi -->
                    <div class="flex justify-end pt-1">
                        <a href="{{ route('password.request') }}" class="text-xs font-semibold text-amber-600 hover:text-amber-700 hover:underline inline-flex items-center space-x-1">
                            <span>Lupa Kata Sandi?</span>
                            <i class="fa-solid fa-arrow-right text-[10px]"></i>
                        </a>
                    </div>
                </div>

                <!-- Remember Me hidden persistence -->
                <input type="hidden" name="remember" value="1">

                <!-- Submit Button -->
                <button 
                    type="submit" 
                    class="w-full py-3.5 px-4 bg-gradient-to-r from-amber-500 via-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-navy-950 font-extrabold text-sm rounded-xl shadow-lg shadow-amber-500/25 transition duration-150 ease-in-out flex items-center justify-center space-x-2 group"
                >
                    <span>Masuk</span>
                    <i class="fa-solid fa-arrow-right text-xs group-hover:translate-x-1 transition-transform"></i>
                </button>
            </form>

            <!-- Divider -->
            <div class="relative flex items-center justify-center my-3">
                <div class="border-t border-slate-200 w-full"></div>
                <span class="bg-white/90 px-3 text-[11px] font-bold text-slate-400 uppercase tracking-wider absolute backdrop-blur-sm">atau</span>
            </div>

            <!-- Secondary Action: Belum punya akun? Daftar Sekarang -->
            <div>
                <a 
                    href="{{ route('register') }}" 
                    class="w-full py-3 px-4 bg-slate-50 hover:bg-slate-100 border border-slate-200 text-slate-700 hover:text-navy-900 font-bold text-xs rounded-xl transition flex items-center justify-center space-x-2 text-center"
                >
                    <i class="fa-regular fa-user text-amber-600"></i>
                    <span>Belum punya akun? <strong class="text-amber-600 hover:underline">Daftar Sekarang</strong></span>
                </a>
            </div>

            <!-- Footer Note -->
            <div class="pt-2 text-center">
                <a href="{{ route('home') }}" class="text-[11px] text-slate-400 hover:text-amber-600 transition inline-flex items-center space-x-1">
                    <i class="fa-solid fa-arrow-left text-[9px]"></i>
                    <span>Kembali ke Beranda Utama</span>
                </a>
            </div>

        </div>
    </div>

    <!-- Toggle Password Visibility Script -->
    <script>
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eyeIcon');

        if (togglePassword && passwordInput && eyeIcon) {
            togglePassword.addEventListener('click', function () {
                const isPassword = passwordInput.getAttribute('type') === 'password';
                passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
                
                if (isPassword) {
                    eyeIcon.classList.remove('fa-eye-slash');
                    eyeIcon.classList.add('fa-eye');
                } else {
                    eyeIcon.classList.remove('fa-eye');
                    eyeIcon.classList.add('fa-eye-slash');
                }
            });
        }
    </script>
</body>
</html>
