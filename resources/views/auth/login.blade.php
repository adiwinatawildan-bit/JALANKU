<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Masuk - {{ \App\Models\SystemSetting::appName() }} Garut</title>

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
                        brand: {
                            blue: '#1d4ed8',
                            hover: '#1e40af',
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
        .scenic-mask {
            clip-path: ellipse(115% 100% at 0% 50%);
        }
        @media (max-width: 1023px) {
            .mobile-header-curve {
                border-bottom-left-radius: 42px;
                border-bottom-right-radius: 42px;
            }
        }
    </style>
</head>
<body class="min-h-full bg-slate-50 text-slate-800 antialiased flex flex-col justify-center">

    <div class="min-h-screen flex flex-col lg:flex-row w-full">

        <!-- LEFT COLUMN: DESKTOP HERO & BRANDING (Visible on lg and up) -->
        <div class="hidden lg:relative lg:flex lg:w-7/12 flex-col justify-between p-12 xl:p-16 overflow-hidden bg-navy-950 text-white select-none">
            <!-- Background Image with Overlay -->
            <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('{{ asset('images/login-bg.jpg') }}');"></div>
            <div class="absolute inset-0 bg-gradient-to-r from-navy-950/70 via-navy-950/40 to-transparent"></div>
            <div class="absolute inset-0 bg-gradient-to-t from-navy-950/80 via-transparent to-navy-950/30"></div>

            <!-- Brand Header -->
            <div class="relative z-10 space-y-4 max-w-xl pt-4">
                <div class="flex items-center space-x-4">
                    <div class="w-16 h-16 rounded-2xl overflow-hidden shadow-2xl bg-white/10 p-1 backdrop-blur-md border border-white/20">
                        <img src="{{ asset('images/jalanku-hex-logo.jpg') }}" alt="Jalanku" class="w-full h-full object-cover rounded-xl">
                    </div>
                    <h1 class="text-4xl xl:text-5xl font-extrabold tracking-tight text-white drop-shadow-sm">Jalanku</h1>
                </div>

                <div class="space-y-2 pt-2">
                    <h2 class="text-2xl xl:text-3xl font-bold text-white/95 leading-snug">
                        Bersama Wujudkan Jalan yang Lebih Baik
                    </h2>
                    <p class="text-xs xl:text-sm text-white/80 leading-relaxed max-w-md">
                        Sistem Informasi Pelaporan dan Penentuan Prioritas Penanganan Kerusakan Jalan Kabupaten Garut
                    </p>
                </div>
            </div>

            <!-- Bottom Highlights (Laporkan, Pantau, Prioritaskan) -->
            <div class="relative z-10 grid grid-cols-3 gap-6 pt-12 border-t border-white/15">
                <div class="space-y-1.5">
                    <div class="w-9 h-9 rounded-xl bg-white/10 backdrop-blur-md flex items-center justify-center text-white text-base border border-white/20 shadow-sm">
                        <i class="fa-regular fa-comment-dots"></i>
                    </div>
                    <h3 class="font-bold text-sm text-white">Laporkan</h3>
                    <p class="text-[11px] text-white/75 leading-tight">Kerusakan jalan dengan mudah</p>
                </div>

                <div class="space-y-1.5">
                    <div class="w-9 h-9 rounded-xl bg-white/10 backdrop-blur-md flex items-center justify-center text-white text-base border border-white/20 shadow-sm">
                        <i class="fa-solid fa-shield-halved"></i>
                    </div>
                    <h3 class="font-bold text-sm text-white">Pantau</h3>
                    <p class="text-[11px] text-white/75 leading-tight">Proses penanganan secara transparan</p>
                </div>

                <div class="space-y-1.5">
                    <div class="w-9 h-9 rounded-xl bg-white/10 backdrop-blur-md flex items-center justify-center text-white text-base border border-white/20 shadow-sm">
                        <i class="fa-solid fa-bullseye"></i>
                    </div>
                    <h3 class="font-bold text-sm text-white">Prioritaskan</h3>
                    <p class="text-[11px] text-white/75 leading-tight">Penanganan sesuai dengan kebutuhan</p>
                </div>
            </div>
        </div>

        <!-- RIGHT COLUMN: LOGIN FORM (Desktop & Mobile) -->
        <div class="w-full lg:w-5/12 flex flex-col justify-between bg-slate-50 relative min-h-screen">

            <!-- MOBILE TOP BANNER (Visible on mobile only) -->
            <div class="lg:hidden relative h-72 sm:h-80 w-full overflow-hidden mobile-header-curve bg-navy-950 shadow-lg">
                <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('{{ asset('images/login-bg.jpg') }}');"></div>
                <div class="absolute inset-0 bg-gradient-to-b from-navy-950/60 via-transparent to-navy-950/70"></div>
                
                <div class="relative z-10 flex flex-col items-center justify-center h-full text-center px-6 -mt-3">
                    <div class="w-16 h-16 rounded-2xl overflow-hidden shadow-2xl bg-white/10 p-1 backdrop-blur-md border border-white/30 mb-2">
                        <img src="{{ asset('images/jalanku-hex-logo.jpg') }}" alt="Jalanku" class="w-full h-full object-cover rounded-xl">
                    </div>
                    <h1 class="text-3xl font-extrabold text-white tracking-tight">Jalanku</h1>
                    <p class="text-xs text-white/90 font-semibold mt-1">Bersama Wujudkan Jalan yang Lebih Baik</p>
                </div>
            </div>

            <!-- FORM CARD CONTAINER -->
            <div class="flex-1 flex items-center justify-center px-6 sm:px-10 lg:px-12 py-8 -mt-6 lg:mt-0 relative z-20">
                <div class="w-full max-w-md bg-white rounded-3xl p-7 sm:p-9 shadow-xl lg:shadow-md border border-slate-100/80 space-y-6">

                    <!-- Card Header -->
                    <div class="text-center space-y-2">
                        <!-- Hexagon Logo visible only on desktop header inside card -->
                        <div class="hidden lg:flex items-center justify-center space-x-2.5 mb-3">
                            <div class="w-10 h-10 rounded-xl overflow-hidden shadow-md bg-slate-100">
                                <img src="{{ asset('images/jalanku-hex-logo.jpg') }}" alt="Jalanku" class="w-full h-full object-cover">
                            </div>
                            <span class="text-2xl font-extrabold text-navy-900 tracking-tight">Jalanku</span>
                        </div>

                        <h2 class="text-xl sm:text-2xl font-extrabold text-navy-900 tracking-tight">
                            <span class="hidden lg:inline">Selamat Datang Kembali</span>
                            <span class="lg:hidden">Selamat Datang</span>
                        </h2>
                        <p class="text-xs text-slate-500 leading-relaxed">
                            Silakan masuk ke akun Anda untuk melanjutkan ke sistem Jalanku.
                        </p>
                    </div>

                    <!-- Flash Message Alerts -->
                    @if(session('success'))
                        <div class="p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center space-x-2">
                            <i class="fa-solid fa-circle-check text-emerald-600"></i>
                            <span>{{ session('success') }}</span>
                        </div>
                    @endif

                    @if(session('status'))
                        <div class="p-3.5 rounded-xl bg-blue-50 border border-blue-200 text-blue-800 text-xs font-semibold flex items-center space-x-2">
                            <i class="fa-solid fa-circle-info text-blue-600"></i>
                            <span>{{ session('status') }}</span>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold space-y-1">
                            <div class="flex items-center space-x-2">
                                <i class="fa-solid fa-circle-exclamation text-rose-600"></i>
                                <span>{{ $errors->first() }}</span>
                            </div>
                        </div>
                    @endif

                    <!-- Login Form -->
                    <form method="POST" action="{{ route('login') }}" class="space-y-4">
                        @csrf

                        <!-- Email atau Username -->
                        <div class="space-y-1.5">
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
                                    class="w-full pl-10 pr-4 py-3 bg-white border border-slate-200 rounded-xl text-xs sm:text-sm font-medium text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent transition shadow-sm"
                                >
                            </div>
                        </div>

                        <!-- Kata Sandi -->
                        <div class="space-y-1.5">
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
                                    class="w-full pl-10 pr-11 py-3 bg-white border border-slate-200 rounded-xl text-xs sm:text-sm font-medium text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent transition shadow-sm"
                                >
                                <button 
                                    type="button" 
                                    id="togglePassword" 
                                    class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 transition focus:outline-none"
                                    tabindex="-1"
                                >
                                    <i class="fa-regular fa-eye-slash text-sm" id="eyeIcon"></i>
                                </button>
                            </div>

                            <!-- Lupa Sandi -->
                            <div class="flex justify-end pt-1">
                                <a href="{{ route('password.request') }}" class="text-xs font-semibold text-blue-600 hover:text-blue-700 hover:underline inline-flex items-center space-x-1">
                                    <span>Lupa Kata Sandi?</span>
                                    <i class="fa-solid fa-arrow-right text-[10px]"></i>
                                </a>
                            </div>
                        </div>

                        <!-- Remember Me checkbox hidden but included for persistence -->
                        <input type="hidden" name="remember" value="1">

                        <!-- Submit Button (Masuk ->) -->
                        <button 
                            type="submit" 
                            class="w-full py-3.5 px-4 bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm rounded-xl shadow-lg shadow-blue-600/25 transition duration-150 ease-in-out flex items-center justify-center space-x-2 group"
                        >
                            <span>Masuk</span>
                            <i class="fa-solid fa-arrow-right text-xs group-hover:translate-x-1 transition-transform"></i>
                        </button>
                    </form>

                    <!-- Divider -->
                    <div class="relative flex items-center justify-center my-4">
                        <div class="border-t border-slate-200 w-full"></div>
                        <span class="bg-white px-3 text-[11px] font-medium text-slate-400 uppercase tracking-wider absolute">atau</span>
                    </div>

                    <!-- Secondary Action: Hubungi Admin / Daftar -->
                    <div>
                        <a 
                            href="{{ route('register') }}" 
                            class="w-full py-3 px-4 bg-slate-50 hover:bg-slate-100 border border-slate-200 text-slate-700 font-semibold text-xs rounded-xl transition flex items-center justify-center space-x-2 text-center"
                        >
                            <i class="fa-regular fa-user text-slate-500"></i>
                            <span>Belum punya akun? Hubungi Admin</span>
                        </a>
                    </div>

                    <!-- Footer: Dinas Komunikasi dan Informatika Garut -->
                    <div class="pt-2 flex items-center justify-center space-x-2 text-slate-500">
                        <div class="w-5 h-5 rounded flex items-center justify-center text-slate-400 text-xs">
                            <i class="fa-solid fa-landmark"></i>
                        </div>
                        <span class="text-[11px] font-medium leading-tight text-center">
                            Dinas Komunikasi dan Informatika<br class="hidden sm:inline"> Kabupaten Garut
                        </span>
                    </div>

                </div>
            </div>

            <!-- Mobile Bottom Decorative Waves -->
            <div class="lg:hidden w-full h-8 bg-gradient-to-r from-blue-600 via-sky-400 to-emerald-400 opacity-20 pointer-events-none mt-auto"></div>

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
