<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Daftar Akun - {{ \App\Models\SystemSetting::appName() }}</title>

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
        @media (max-width: 1023px) {
            .mobile-header-curve {
                border-bottom-left-radius: 36px;
                border-bottom-right-radius: 36px;
            }
        }
    </style>
</head>
<body class="min-h-full bg-slate-100 text-slate-800 antialiased flex flex-col justify-center selection:bg-amber-500 selection:text-navy-950">

    <div class="min-h-screen flex flex-col lg:flex-row w-full">

        <!-- LEFT COLUMN: DESKTOP HERO & BRANDING (50% Balanced Width) -->
        <div class="hidden lg:relative lg:flex lg:w-1/2 flex-col justify-between p-12 xl:p-16 overflow-hidden bg-navy-950 text-white select-none">
            <!-- Background Image with Depth Overlays -->
            <div class="absolute inset-0 bg-cover bg-center scale-105 transition-transform duration-1000" style="background-image: url('{{ asset('images/login-bg.jpg') }}');"></div>
            <div class="absolute inset-0 bg-gradient-to-r from-navy-950/85 via-navy-950/50 to-navy-950/20"></div>
            <div class="absolute inset-0 bg-gradient-to-t from-navy-950 via-transparent to-navy-950/40"></div>

            <!-- Decorative Ambient Glow -->
            <div class="absolute -top-24 -left-24 w-96 h-96 bg-amber-500/15 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -bottom-20 right-10 w-80 h-80 bg-amber-500/10 rounded-full blur-3xl pointer-events-none"></div>

            <!-- Brand Header -->
            <div class="relative z-10 space-y-5 max-w-lg pt-4">
                <div class="flex items-center space-x-4">
                    <div class="w-16 h-16 rounded-2xl overflow-hidden shadow-2xl bg-navy-900/60 p-1 backdrop-blur-xl border border-amber-500/30 ring-2 ring-amber-500/20">
                        <img src="{{ asset('images/jalanku-hex-logo.jpg') }}" alt="{{ \App\Models\SystemSetting::appName() }}" class="w-full h-full object-cover rounded-xl">
                    </div>
                    <div>
                        <span class="text-xs font-bold text-amber-400 uppercase tracking-widest block">Registrasi Akun Baru</span>
                        <h1 class="text-4xl xl:text-5xl font-extrabold tracking-tight text-white drop-shadow-sm">{{ \App\Models\SystemSetting::appName() }}</h1>
                    </div>
                </div>

                <div class="space-y-2 pt-2 bg-navy-950/40 backdrop-blur-md p-5 rounded-2xl border border-white/10 shadow-lg">
                    <h2 class="text-xl xl:text-2xl font-bold text-white leading-snug">
                        Partisipasi Aktif Menjaga Fasilitas Jalan
                    </h2>
                    <p class="text-xs xl:text-sm text-slate-300 leading-relaxed">
                        Buat akun masyarakat untuk melaporkan kerusakan jalan, memantau progres perbaikan, dan mengakses transparansi penanganan infrastruktur.
                    </p>
                </div>
            </div>

            <!-- Bottom Highlights -->
            <div class="relative z-10 grid grid-cols-3 gap-4 pt-8 border-t border-white/15">
                <div class="space-y-1.5 bg-navy-900/40 backdrop-blur-md p-3.5 rounded-xl border border-white/10">
                    <div class="w-8 h-8 rounded-lg bg-amber-500/20 text-amber-400 flex items-center justify-center text-sm border border-amber-500/30">
                        <i class="fa-regular fa-comment-dots"></i>
                    </div>
                    <h3 class="font-bold text-xs text-white">Laporkan</h3>
                    <p class="text-[10px] text-slate-300 leading-tight">Kerusakan jalan dengan mudah</p>
                </div>

                <div class="space-y-1.5 bg-navy-900/40 backdrop-blur-md p-3.5 rounded-xl border border-white/10">
                    <div class="w-8 h-8 rounded-lg bg-amber-500/20 text-amber-400 flex items-center justify-center text-sm border border-amber-500/30">
                        <i class="fa-solid fa-shield-halved"></i>
                    </div>
                    <h3 class="font-bold text-xs text-white">Pantau</h3>
                    <p class="text-[10px] text-slate-300 leading-tight">Proses penanganan transparan</p>
                </div>

                <div class="space-y-1.5 bg-navy-900/40 backdrop-blur-md p-3.5 rounded-xl border border-white/10">
                    <div class="w-8 h-8 rounded-lg bg-amber-500/20 text-amber-400 flex items-center justify-center text-sm border border-amber-500/30">
                        <i class="fa-solid fa-bullseye"></i>
                    </div>
                    <h3 class="font-bold text-xs text-white">Prioritaskan</h3>
                    <p class="text-[10px] text-slate-300 leading-tight">Penanganan berbasis SPK TOPSIS</p>
                </div>
            </div>
        </div>

        <!-- RIGHT COLUMN: REGISTER FORM (50% Balanced Width) -->
        <div class="w-full lg:w-1/2 flex flex-col justify-center items-center bg-slate-50 relative min-h-screen p-6 sm:p-10 lg:p-12">

            <!-- MOBILE TOP BANNER (Visible on mobile only) -->
            <div class="lg:hidden relative h-56 sm:h-60 w-full overflow-hidden mobile-header-curve bg-navy-950 shadow-lg -mx-6 sm:-mx-10 -mt-6 sm:-mt-10 mb-6">
                <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('{{ asset('images/login-bg.jpg') }}');"></div>
                <div class="absolute inset-0 bg-gradient-to-b from-navy-950/70 via-navy-950/40 to-navy-950/80"></div>
                
                <div class="relative z-10 flex flex-col items-center justify-center h-full text-center px-6 -mt-2">
                    <div class="w-12 h-12 rounded-2xl overflow-hidden shadow-2xl bg-navy-900/80 p-1 backdrop-blur-md border border-amber-500/30 ring-2 ring-amber-500/20 mb-1.5">
                        <img src="{{ asset('images/jalanku-hex-logo.jpg') }}" alt="{{ \App\Models\SystemSetting::appName() }}" class="w-full h-full object-cover rounded-xl">
                    </div>
                    <h1 class="text-2xl font-extrabold text-white tracking-tight">Daftar Akun</h1>
                    <p class="text-xs text-amber-300 font-semibold">Buat akun masyarakat baru</p>
                </div>
            </div>

            <!-- ELEVATED FORM CARD -->
            <div class="w-full max-w-md bg-white rounded-3xl p-7 sm:p-9 shadow-xl border border-slate-200/80 space-y-4 relative z-10">

                <!-- Card Header -->
                <div class="text-center space-y-1">
                    <div class="hidden lg:flex items-center justify-center space-x-2.5 mb-1">
                        <div class="w-9 h-9 rounded-xl overflow-hidden shadow-md bg-navy-950 p-0.5 border border-amber-500/30">
                            <img src="{{ asset('images/jalanku-hex-logo.jpg') }}" alt="{{ \App\Models\SystemSetting::appName() }}" class="w-full h-full object-cover rounded-lg">
                        </div>
                        <span class="text-xl font-extrabold text-navy-900 tracking-tight">{{ \App\Models\SystemSetting::appName() }}</span>
                    </div>

                    <h2 class="text-xl sm:text-2xl font-extrabold text-navy-900 tracking-tight">
                        Daftar Akun Masyarakat
                    </h2>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Lengkapi data diri Anda untuk mulai melaporkan kerusakan jalan.
                    </p>
                </div>

                <!-- Flash Errors -->
                @if($errors->any())
                    <div class="p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold space-y-1">
                        <div class="flex items-center space-x-2">
                            <i class="fa-solid fa-circle-exclamation text-rose-600"></i>
                            <span>{{ $errors->first() }}</span>
                        </div>
                    </div>
                @endif

                <!-- Register Form -->
                <form method="POST" action="{{ route('register') }}" class="space-y-3">
                    @csrf

                    <!-- Nama Lengkap -->
                    <div class="space-y-1">
                        <label for="name" class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider">
                            Nama Lengkap <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <i class="fa-solid fa-user text-xs"></i>
                            </div>
                            <input 
                                id="name" 
                                type="text" 
                                name="name" 
                                value="{{ old('name') }}" 
                                required 
                                autofocus 
                                placeholder="Nama sesuai KTP/Identitas" 
                                class="w-full pl-9 pr-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-medium text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:bg-white focus:border-amber-500 transition shadow-sm"
                            >
                        </div>
                    </div>

                    <!-- Email -->
                    <div class="space-y-1">
                        <label for="email" class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider">
                            Alamat Email <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <i class="fa-solid fa-envelope text-xs"></i>
                            </div>
                            <input 
                                id="email" 
                                type="email" 
                                name="email" 
                                value="{{ old('email') }}" 
                                required 
                                placeholder="nama@email.com" 
                                class="w-full pl-9 pr-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-medium text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:bg-white focus:border-amber-500 transition shadow-sm"
                            >
                        </div>
                    </div>

                    <!-- Phone -->
                    <div class="space-y-1">
                        <label for="phone" class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider">
                            Nomor HP / WhatsApp <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <i class="fa-solid fa-phone text-xs"></i>
                            </div>
                            <input 
                                id="phone" 
                                type="text" 
                                name="phone" 
                                value="{{ old('phone') }}" 
                                required 
                                placeholder="08xxxxxxxxxx" 
                                class="w-full pl-9 pr-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-medium text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:bg-white focus:border-amber-500 transition shadow-sm"
                            >
                        </div>
                    </div>

                    <!-- Password Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div class="space-y-1">
                            <label for="password" class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider">
                                Password <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                    <i class="fa-solid fa-lock text-xs"></i>
                                </div>
                                <input 
                                    id="password" 
                                    type="password" 
                                    name="password" 
                                    required 
                                    placeholder="Min. 6 karakter" 
                                    class="w-full pl-8 pr-3 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-medium text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:bg-white focus:border-amber-500 transition shadow-sm"
                                >
                            </div>
                        </div>

                        <div class="space-y-1">
                            <label for="password_confirmation" class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider">
                                Ulangi Password <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                    <i class="fa-solid fa-shield-halved text-xs"></i>
                                </div>
                                <input 
                                    id="password_confirmation" 
                                    type="password" 
                                    name="password_confirmation" 
                                    required 
                                    placeholder="Ulangi password" 
                                    class="w-full pl-8 pr-3 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-medium text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:bg-white focus:border-amber-500 transition shadow-sm"
                                >
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button 
                        type="submit" 
                        class="w-full py-3.5 px-4 bg-gradient-to-r from-amber-500 via-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-navy-950 font-extrabold text-sm rounded-xl shadow-lg shadow-amber-500/25 transition duration-150 ease-in-out flex items-center justify-center space-x-2 group mt-2"
                    >
                        <i class="fa-solid fa-user-plus text-xs"></i>
                        <span>Daftar Sekarang</span>
                    </button>
                </form>

                <!-- Back to Login Link -->
                <div class="border-t border-slate-100 pt-3 text-center">
                    <p class="text-xs text-slate-500">
                        Sudah memiliki akun?
                        <a href="{{ route('login') }}" class="font-bold text-amber-600 hover:text-amber-700 hover:underline">
                            Masuk ke Akun
                        </a>
                    </p>
                </div>

            </div>

        </div>

    </div>
</body>
</html>
