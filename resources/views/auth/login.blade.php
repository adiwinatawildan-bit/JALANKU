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
<body class="min-h-screen lg:h-screen lg:overflow-hidden bg-navy-950 text-slate-100 antialiased relative selection:bg-amber-500 selection:text-navy-950 flex items-center justify-center p-4 sm:p-6 overflow-y-auto">

    <!-- FULL SCREEN WARM VIBRANT BACKGROUND IMAGE WITH AMBER/ORANGE GRADIENT -->
    <div class="fixed inset-0 z-0 overflow-hidden pointer-events-none">
        <div class="absolute inset-0 bg-cover bg-center scale-105 transition-transform duration-1000" style="background-image: url('{{ asset('images/login-bg.jpg') }}');"></div>
        <!-- Warm sunset amber overlay that keeps the photo clear while blending seamlessly -->
        <div class="absolute inset-0 bg-gradient-to-tr from-navy-950/85 via-navy-900/60 to-amber-950/40 backdrop-blur-[1px]"></div>
        <!-- Ambient Warm Glow Lights -->
        <div class="absolute -top-24 -left-24 w-96 h-96 bg-amber-500/20 rounded-full blur-3xl"></div>
        <div class="absolute top-1/2 -right-32 w-96 h-96 bg-orange-500/15 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-24 left-1/3 w-96 h-96 bg-amber-600/15 rounded-full blur-3xl"></div>
    </div>

    <!-- CENTERED BLENDED GLASS FORM CARD -->
    <div class="w-full max-w-md relative z-10 my-auto">
        <div class="bg-navy-950/80 backdrop-blur-2xl rounded-3xl p-6 sm:p-8 shadow-[0_25px_60px_-15px_rgba(0,0,0,0.8)] border border-amber-500/30 ring-1 ring-white/10 space-y-5 text-white">

            <!-- Card Header & Branding -->
            <div class="text-center space-y-2.5">
                <!-- App Logo -->
                <div class="inline-flex items-center justify-center">
                    <div class="w-14 h-14 rounded-2xl overflow-hidden shadow-2xl bg-navy-900/90 p-1 border border-amber-500/40 ring-4 ring-amber-500/20">
                        <img src="{{ asset('images/jalanku-hex-logo.jpg') }}" alt="{{ \App\Models\SystemSetting::appName() }}" class="w-full h-full object-cover rounded-xl">
                    </div>
                </div>

                <div>
                    <span class="text-[10px] font-bold text-amber-400 uppercase tracking-widest block mb-0.5">Portal Pelayanan Publik</span>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight drop-shadow">{{ \App\Models\SystemSetting::appName() }}</h1>
                </div>

                <div class="space-y-0.5">
                    <h2 class="text-base sm:text-lg font-bold text-amber-300 tracking-tight">
                        Selamat Datang Kembali
                    </h2>
                    <p class="text-xs text-slate-300 leading-relaxed max-w-xs mx-auto">
                        Silakan masuk ke akun Anda untuk melanjutkan ke sistem pelaporan.
                    </p>
                </div>
            </div>

            <!-- Flash Message Alerts -->
            @if(session('success'))
                <div class="p-3 rounded-xl bg-emerald-950/80 border border-emerald-500/40 text-emerald-300 text-xs font-semibold flex items-center space-x-2 backdrop-blur-sm">
                    <i class="fa-solid fa-circle-check text-emerald-400 shrink-0"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if(session('status'))
                <div class="p-3 rounded-xl bg-amber-950/80 border border-amber-500/40 text-amber-300 text-xs font-semibold flex items-center space-x-2 backdrop-blur-sm">
                    <i class="fa-solid fa-circle-info text-amber-400 shrink-0"></i>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="p-3 rounded-xl bg-rose-950/80 border border-rose-500/40 text-rose-300 text-xs font-semibold space-y-1 backdrop-blur-sm">
                    <div class="flex items-center space-x-2">
                        <i class="fa-solid fa-circle-exclamation text-rose-400 shrink-0"></i>
                        <span>{{ $errors->first() }}</span>
                    </div>
                </div>
            @endif

            <!-- Login Form -->
            <form method="POST" action="{{ route('login') }}" class="space-y-3.5">
                @csrf

                <!-- Email atau Username -->
                <div class="space-y-1">
                    <label for="email" class="block text-[11px] font-bold text-amber-400 uppercase tracking-wider">Email atau Username</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i class="fa-regular fa-user text-xs text-amber-400/80"></i>
                        </div>
                        <input 
                            id="email" 
                            type="text" 
                            name="email" 
                            value="{{ old('email') }}" 
                            required 
                            autofocus 
                            placeholder="Email atau Username" 
                            class="w-full pl-9 pr-4 py-2.5 bg-slate-900/80 border border-slate-700/90 rounded-xl text-xs sm:text-sm font-medium text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-amber-500/50 focus:border-amber-400 focus:bg-slate-900 transition shadow-inner"
                        >
                    </div>
                </div>

                <!-- Kata Sandi -->
                <div class="space-y-1">
                    <label for="password" class="block text-[11px] font-bold text-amber-400 uppercase tracking-wider">Kata Sandi</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i class="fa-solid fa-lock text-xs text-amber-400/80"></i>
                        </div>
                        <input 
                            id="password" 
                            type="password" 
                            name="password" 
                            required 
                            placeholder="Kata Sandi" 
                            class="w-full pl-9 pr-10 py-2.5 bg-slate-900/80 border border-slate-700/90 rounded-xl text-xs sm:text-sm font-medium text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-amber-500/50 focus:border-amber-400 focus:bg-slate-900 transition shadow-inner"
                        >
                        <button 
                            type="button" 
                            id="togglePassword" 
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-amber-400 transition focus:outline-none"
                            tabindex="-1"
                        >
                            <i class="fa-regular fa-eye-slash text-xs" id="eyeIcon"></i>
                        </button>
                    </div>

                    <!-- Lupa Sandi -->
                    <div class="flex justify-end pt-0.5">
                        <a href="{{ route('password.request') }}" class="text-xs font-semibold text-amber-400 hover:text-amber-300 hover:underline inline-flex items-center space-x-1">
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
                    class="w-full py-3 px-4 bg-gradient-to-r from-amber-500 via-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-navy-950 font-extrabold text-sm rounded-xl shadow-lg shadow-amber-500/30 transition duration-150 ease-in-out flex items-center justify-center space-x-2 group"
                >
                    <span>Masuk</span>
                    <i class="fa-solid fa-arrow-right text-xs group-hover:translate-x-1 transition-transform"></i>
                </button>
            </form>

            <!-- Divider -->
            <div class="relative flex items-center justify-center my-2">
                <div class="border-t border-slate-700/70 w-full"></div>
                <span class="bg-navy-950 px-3 text-[10px] font-bold text-slate-400 uppercase tracking-wider absolute">atau</span>
            </div>

            <!-- Secondary Action: Belum punya akun? Daftar Sekarang -->
            <div>
                <a 
                    href="{{ route('register') }}" 
                    class="w-full py-2.5 px-4 bg-slate-900/60 hover:bg-slate-800/80 border border-white/10 text-slate-300 hover:text-white font-bold text-xs rounded-xl transition flex items-center justify-center space-x-2 text-center"
                >
                    <i class="fa-regular fa-user text-amber-400"></i>
                    <span>Belum punya akun? <strong class="text-amber-400 hover:underline">Daftar Sekarang</strong></span>
                </a>
            </div>

            <!-- Footer Note -->
            <div class="pt-1 text-center">
                <a href="{{ route('home') }}" class="text-[11px] text-slate-400 hover:text-amber-400 transition inline-flex items-center space-x-1">
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
