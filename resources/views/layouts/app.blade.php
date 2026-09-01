<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', \App\Models\SystemSetting::appName() . ' - Sistem Pengaduan, Monitoring & Rekomendasi Perbaikan Jalan Umum')</title>
    <meta name="description" content="@yield('meta_description', 'Portal layanan publik terpadu untuk pengaduan, pemantauan progres, analisis AI YOLO, dan rekomendasi prioritas TOPSIS kerusakan jalan umum.')">
    
    <!-- Google Fonts: Plus Jakarta Sans + JetBrains Mono -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        navy: {
                            800: '#1e293b',
                            900: '#0f172a',
                            950: '#090d16',
                        },
                        amber: {
                            500: '#f59e0b',
                            600: '#d97706',
                        },
                    },
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                        mono: ['"JetBrains Mono"', 'ui-monospace', 'SFMono-Regular', 'monospace'],
                    }
                }
            }
        }
    </script>
    
    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    
    <!-- GLightbox CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css" />

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">

    @stack('styles')
</head>
<body class="bg-slate-50 text-slate-800 antialiased min-h-screen flex flex-col selection:bg-amber-500 selection:text-white">

    <!-- Officer Preview Bar (When Petugas/OPD/Admin views Public Portal) -->
    @auth
        @if(!Auth::user()->isMasyarakat())
            @php
                $portalRoute = route('home');
                $roleLabel = 'Petugas';
                if (Auth::user()->isSuperAdmin()) {
                    $portalRoute = route('superadmin.dashboard');
                    $roleLabel = 'Super Admin';
                } elseif (Auth::user()->isAdmin()) {
                    $portalRoute = route('admin.dashboard');
                    $roleLabel = 'Administrator';
                } elseif (Auth::user()->isOpd()) {
                    $portalRoute = route('opd.dashboard');
                    $roleLabel = 'OPD / Petugas Lapangan';
                }
            @endphp
            <div class="bg-gradient-to-r from-amber-600 via-amber-500 to-amber-600 text-navy-950 px-4 py-2 text-xs font-bold shadow-md border-b border-amber-700/30 z-50">
                <div class="max-w-7xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-2">
                    <div class="flex items-center space-x-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-navy-950 animate-ping"></span>
                        <span>Mode Pratinjau: Anda sedang melihat <strong>Portal Publik</strong> (versi yang dilihat masyarakat)</span>
                    </div>
                    <a href="{{ $portalRoute }}" class="inline-flex items-center space-x-2 px-3.5 py-1 bg-navy-950 hover:bg-navy-900 text-amber-400 font-extrabold rounded-lg shadow transition text-xs">
                        <i class="fa-solid fa-arrow-left"></i>
                        <span>Kembali ke Portal Petugas ({{ $roleLabel }})</span>
                    </a>
                </div>
            </div>
        @endif
    @endauth

    <!-- Top Notice Bar -->
    <div class="bg-navy-950 text-slate-300 text-xs py-1.5 px-4 border-b border-slate-800">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <div class="flex items-center space-x-2">
                <span class="inline-block w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                <span>Portal Layanan Publik Pengaduan & Transparansi Perbaikan Jalan</span>
            </div>
            <div class="flex items-center space-x-4">
                <span><i class="fa-solid fa-clock mr-1 text-amber-500"></i> Layanan 24 Jam</span>
                <span class="hidden md:inline text-slate-500">|</span>
                <span class="hidden md:inline"><i class="fa-solid fa-shield-halved mr-1 text-sky-400"></i> Data Terenkripsi</span>
            </div>
        </div>
    </div>

    <!-- 12. NAVBAR -->
    <header class="sticky top-0 z-40 bg-navy-950/95 backdrop-blur-md text-white border-b border-slate-800/80 shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-18 py-3">
                
                <!-- Logo & Branding -->
                <a href="{{ route('home') }}" class="flex items-center space-x-3 group shrink-0">
                    <img src="{{ \App\Models\SystemSetting::getLogo() }}" alt="{{ \App\Models\SystemSetting::appName() }} Logo" class="w-10 h-10 rounded-xl object-contain bg-navy-900 border border-slate-700/80 p-0.5 shadow-md shadow-amber-500/10 group-hover:scale-105 transition duration-200">
                    <div>
                        <div class="flex items-center space-x-2">
                            <span class="text-xl font-extrabold tracking-tight text-white font-sans">{{ \App\Models\SystemSetting::appName() }}</span>
                        </div>
                        <p class="text-[10px] text-slate-400 font-medium tracking-wide">{{ \App\Models\SystemSetting::appSlogan() }}</p>
                    </div>
                </a>

                <!-- Desktop Nav Menu -->
                <nav class="hidden lg:flex items-center space-x-1 font-sans">
                    <a href="{{ route('home') }}" class="px-3.5 py-2 rounded-xl text-xs font-bold transition duration-150 {{ request()->routeIs('home') ? 'bg-amber-500/15 text-amber-400 border border-amber-500/30' : 'text-slate-300 hover:text-white hover:bg-slate-800/60' }}">
                        Beranda
                    </a>
                    <a href="{{ route('public.peta') }}" class="px-3.5 py-2 rounded-xl text-xs font-bold transition duration-150 {{ request()->routeIs('public.peta') ? 'bg-amber-500/15 text-amber-400 border border-amber-500/30' : 'text-slate-300 hover:text-white hover:bg-slate-800/60' }}">
                        Peta Jalan
                    </a>
                    <a href="{{ route('public.reports.index') }}" class="px-3.5 py-2 rounded-xl text-xs font-bold transition duration-150 {{ request()->routeIs('public.reports.*') ? 'bg-amber-500/15 text-amber-400 border border-amber-500/30' : 'text-slate-300 hover:text-white hover:bg-slate-800/60' }}">
                        Laporan Publik
                    </a>
                    <a href="{{ route('public.statistik') }}" class="px-3.5 py-2 rounded-xl text-xs font-bold transition duration-150 {{ request()->routeIs('public.statistik') ? 'bg-amber-500/15 text-amber-400 border border-amber-500/30' : 'text-slate-300 hover:text-white hover:bg-slate-800/60' }}">
                        Statistik
                    </a>
                    <a href="{{ route('public.cara-kerja') }}" class="px-3.5 py-2 rounded-xl text-xs font-bold transition duration-150 {{ request()->routeIs('public.cara-kerja') ? 'bg-amber-500/15 text-amber-400 border border-amber-500/30' : 'text-slate-300 hover:text-white hover:bg-slate-800/60' }}">
                        Cara Kerja
                    </a>
                    <a href="{{ route('public.tentang') }}" class="px-3.5 py-2 rounded-xl text-xs font-bold transition duration-150 {{ request()->routeIs('public.tentang') ? 'bg-amber-500/15 text-amber-400 border border-amber-500/30' : 'text-slate-300 hover:text-white hover:bg-slate-800/60' }}">
                        Tentang
                    </a>
                </nav>

                <!-- Action Button & User Section -->
                <div class="hidden sm:flex items-center space-x-3">
                    @auth
                        @php
                            $userPortalRoute = route('home');
                            if (Auth::user()->isSuperAdmin()) {
                                $userPortalRoute = route('superadmin.dashboard');
                            } elseif (Auth::user()->isAdmin()) {
                                $userPortalRoute = route('admin.dashboard');
                            } elseif (Auth::user()->isOpd()) {
                                $userPortalRoute = route('opd.dashboard');
                            } elseif (Auth::user()->isMasyarakat()) {
                                $userPortalRoute = route('masyarakat.dashboard');
                            }
                        @endphp

                        <!-- User Portal Dropdown with Stable Click/Hover Bridge -->
                        <div class="relative" id="user-dropdown-container">
                            <button type="button" id="user-dropdown-btn" class="flex items-center space-x-2 bg-slate-800/90 hover:bg-slate-700 px-3 py-1.5 rounded-xl border border-slate-700 text-xs text-slate-200 transition">
                                @if(Auth::user()->getAvatar())
                                    <img src="{{ Auth::user()->getAvatar() }}" alt="{{ Auth::user()->name }}" class="w-6 h-6 rounded-full object-cover border border-amber-400">
                                @else
                                    <i class="fa-solid fa-circle-user text-amber-400 text-sm"></i>
                                @endif
                                <span class="font-semibold">{{ Str::limit(Auth::user()->name, 14) }}</span>
                                <span class="text-[9px] px-1.5 py-0.5 rounded bg-navy-950 text-amber-400 uppercase font-bold border border-slate-700">{{ Auth::user()->role->name }}</span>
                                <i id="user-dropdown-icon" class="fa-solid fa-chevron-down text-[10px] text-slate-400 transition duration-200"></i>
                            </button>

                            <!-- Dropdown Menu with Invisible Bridge (-top-3) to prevent early close -->
                            <div id="user-dropdown-menu" class="hidden absolute right-0 top-full pt-2 w-60 z-50">
                                <div class="bg-navy-900 border border-slate-700 rounded-2xl shadow-2xl py-2 overflow-hidden backdrop-blur-xl">
                                    
                                    @if(!Auth::user()->isMasyarakat())
                                        <!-- Dedicated Back to Officer Portal Link -->
                                        <a href="{{ $userPortalRoute }}" class="flex items-center px-3.5 py-2 text-xs font-bold text-amber-400 bg-amber-500/10 hover:bg-amber-500/20 border border-amber-500/20 rounded-xl mx-2 mb-2 transition">
                                            <i class="fa-solid fa-arrow-left mr-2"></i> Kembali ke Portal Petugas
                                        </a>
                                    @endif

                                    @if(Auth::user()->isMasyarakat())
                                        <a href="{{ route('masyarakat.dashboard') }}" class="flex items-center px-4 py-2.5 text-xs font-medium text-slate-200 hover:bg-slate-800 hover:text-amber-400">
                                            <i class="fa-solid fa-gauge-high mr-2.5 text-amber-400 w-4"></i> Dashboard Saya
                                        </a>
                                        <a href="{{ route('masyarakat.reports.create') }}" class="flex items-center px-4 py-2.5 text-xs font-medium text-slate-200 hover:bg-slate-800 hover:text-amber-400">
                                            <i class="fa-solid fa-plus-circle mr-2.5 text-emerald-400 w-4"></i> Buat Laporan Baru
                                        </a>
                                        <a href="{{ route('masyarakat.reports.index') }}" class="flex items-center px-4 py-2.5 text-xs font-medium text-slate-200 hover:bg-slate-800 hover:text-amber-400">
                                            <i class="fa-solid fa-list-check mr-2.5 text-sky-400 w-4"></i> Laporan Saya
                                        </a>
                                    @elseif(Auth::user()->isAdmin())
                                        <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-2.5 text-xs font-medium text-slate-200 hover:bg-slate-800 hover:text-amber-400">
                                            <i class="fa-solid fa-chart-line mr-2.5 text-amber-400 w-4"></i> Dashboard Admin
                                        </a>
                                        <a href="{{ route('admin.reports.index') }}" class="flex items-center px-4 py-2.5 text-xs font-medium text-slate-200 hover:bg-slate-800 hover:text-amber-400">
                                            <i class="fa-solid fa-list-check mr-2.5 text-sky-400 w-4"></i> Kelola Laporan
                                        </a>
                                    @elseif(Auth::user()->isOpd())
                                        <a href="{{ route('opd.dashboard') }}" class="flex items-center px-4 py-2.5 text-xs font-medium text-slate-200 hover:bg-slate-800 hover:text-amber-400">
                                            <i class="fa-solid fa-helmet-safety mr-2.5 text-amber-400 w-4"></i> Dashboard OPD
                                        </a>
                                        <a href="{{ route('opd.tasks.index') }}" class="flex items-center px-4 py-2.5 text-xs font-medium text-slate-200 hover:bg-slate-800 hover:text-amber-400">
                                            <i class="fa-solid fa-list-check mr-2.5 text-sky-400 w-4"></i> Daftar Tugas Perbaikan
                                        </a>
                                    @elseif(Auth::user()->isSuperAdmin())
                                        <a href="{{ route('superadmin.dashboard') }}" class="flex items-center px-4 py-2.5 text-xs font-medium text-slate-200 hover:bg-slate-800 hover:text-amber-400">
                                            <i class="fa-solid fa-sliders mr-2.5 text-amber-400 w-4"></i> Super Admin Panel
                                        </a>
                                    @endif

                                    <a href="{{ route('profile') }}" class="flex items-center px-4 py-2.5 text-xs font-medium text-slate-200 hover:bg-slate-800">
                                        <i class="fa-solid fa-user-gear mr-2.5 text-slate-400 w-4"></i> Edit Profil
                                    </a>
                                    <div class="border-t border-slate-800 my-1"></div>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="w-full text-left flex items-center px-4 py-2.5 text-xs font-medium text-rose-400 hover:bg-rose-950/40">
                                            <i class="fa-solid fa-arrow-right-from-bracket mr-2.5 w-4"></i> Keluar (Logout)
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-200 hover:text-white hover:bg-slate-800/80 transition">
                            <i class="fa-solid fa-right-to-bracket mr-1.5 text-amber-400"></i> Masuk
                        </a>
                    @endauth

                    <!-- Button Laporkan Kerusakan -->
                    <a href="{{ Auth::check() ? route('masyarakat.reports.create') : route('login') }}" class="inline-flex items-center space-x-2 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-navy-950 font-extrabold text-xs px-4 py-2.5 rounded-xl shadow-lg shadow-amber-500/20 hover:scale-[1.02] active:scale-[0.98] transition duration-150">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                        <span>Laporkan Kerusakan</span>
                    </a>
                </div>

                <!-- Mobile Menu Button -->
                <div class="flex items-center space-x-2 lg:hidden">
                    <button id="mobile-menu-button" type="button" class="p-2.5 rounded-xl text-slate-300 hover:text-white hover:bg-slate-800 focus:outline-none">
                        <i class="fa-solid fa-bars text-xl"></i>
                    </button>
                </div>

            </div>
        </div>

        <!-- Mobile Navigation Menu -->
        <div id="mobile-menu" class="hidden lg:hidden border-t border-slate-800 bg-navy-950 px-4 pt-3 pb-6 space-y-2">
            <a href="{{ route('home') }}" class="block px-3 py-2 rounded-xl text-sm font-semibold {{ request()->routeIs('home') ? 'bg-amber-500/15 text-amber-400' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">Beranda</a>
            <a href="{{ route('public.peta') }}" class="block px-3 py-2 rounded-xl text-sm font-semibold {{ request()->routeIs('public.peta') ? 'bg-amber-500/15 text-amber-400' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">Peta Jalan</a>
            <a href="{{ route('public.reports.index') }}" class="block px-3 py-2 rounded-xl text-sm font-semibold {{ request()->routeIs('public.reports.*') ? 'bg-amber-500/15 text-amber-400' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">Laporan Publik</a>
            <a href="{{ route('public.statistik') }}" class="block px-3 py-2 rounded-xl text-sm font-semibold {{ request()->routeIs('public.statistik') ? 'bg-amber-500/15 text-amber-400' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">Statistik</a>
            <a href="{{ route('public.cara-kerja') }}" class="block px-3 py-2 rounded-xl text-sm font-semibold {{ request()->routeIs('public.cara-kerja') ? 'bg-amber-500/15 text-amber-400' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">Cara Kerja</a>
            <a href="{{ route('public.tentang') }}" class="block px-3 py-2 rounded-xl text-sm font-semibold {{ request()->routeIs('public.tentang') ? 'bg-amber-500/15 text-amber-400' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">Tentang</a>
            <div class="pt-3 border-t border-slate-800 flex flex-col space-y-2">
                @auth
                    @if(!Auth::user()->isMasyarakat())
                        <a href="{{ $userPortalRoute }}" class="block px-3 py-2 rounded-xl text-xs font-bold text-amber-400 bg-amber-500/20">
                            <i class="fa-solid fa-arrow-left mr-2"></i> Kembali ke Portal Petugas
                        </a>
                    @endif
                    <a href="{{ route('profile') }}" class="flex items-center space-x-2 px-3 py-2 rounded-xl text-xs font-semibold text-slate-300 bg-slate-800">
                        @if(Auth::user()->getAvatar())
                            <img src="{{ Auth::user()->getAvatar() }}" alt="{{ Auth::user()->name }}" class="w-5 h-5 rounded-full object-cover border border-amber-400">
                        @else
                            <i class="fa-solid fa-user text-amber-400"></i>
                        @endif
                        <span>Akun: {{ Auth::user()->name }}</span>
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-left px-3 py-2 rounded-xl text-xs font-semibold text-rose-400 bg-slate-900">
                            <i class="fa-solid fa-right-from-bracket mr-2"></i> Logout
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="block text-center py-2.5 rounded-xl text-xs font-bold bg-slate-800 text-white">Login Masuk</a>
                @endauth
                <a href="{{ Auth::check() ? route('masyarakat.reports.create') : route('login') }}" class="block text-center py-2.5 rounded-xl text-xs font-bold bg-amber-500 text-navy-950">
                    <i class="fa-solid fa-triangle-exclamation mr-1.5"></i> Laporkan Kerusakan
                </a>
            </div>
        </div>
    </header>

    <!-- Flash Alerts -->
    <main class="flex-grow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4">
            @if(session('success'))
                <div class="mb-6 flex items-start p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-900 shadow-sm animate-fade-in" role="alert">
                    <i class="fa-solid fa-circle-check text-emerald-600 text-xl mr-3 mt-0.5"></i>
                    <div class="flex-1">
                        <p class="font-bold text-sm">Berhasil!</p>
                        <p class="text-sm text-emerald-800">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 flex items-start p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-900 shadow-sm animate-fade-in" role="alert">
                    <i class="fa-solid fa-circle-xmark text-rose-600 text-xl mr-3 mt-0.5"></i>
                    <div class="flex-1">
                        <p class="font-bold text-sm">Terjadi Kesalahan!</p>
                        <p class="text-sm text-rose-800">{{ session('error') }}</p>
                    </div>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-6 p-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-900 shadow-sm">
                    <div class="flex items-center mb-2">
                        <i class="fa-solid fa-triangle-exclamation text-amber-600 text-lg mr-2"></i>
                        <h4 class="font-bold text-sm">Periksa kembali data yang Anda masukkan:</h4>
                    </div>
                    <ul class="list-disc list-inside text-sm text-amber-800 space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-navy-950 text-slate-400 text-sm border-t border-slate-800 mt-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-10">
                
                <!-- Col 1: Branding -->
                <div class="md:col-span-2 space-y-4">
                    <a href="{{ route('home') }}" class="flex items-center space-x-3 group">
                        <img src="{{ \App\Models\SystemSetting::getLogo() }}" alt="{{ \App\Models\SystemSetting::appName() }} Logo" class="w-10 h-10 rounded-xl object-contain bg-navy-900 border border-slate-700/80 p-0.5 shadow-md shadow-amber-500/10 group-hover:scale-105 transition duration-200">
                        <span class="text-2xl font-extrabold text-white tracking-tight">{{ \App\Models\SystemSetting::appName() }}</span>
                    </a>
                    <p class="text-slate-400 text-sm leading-relaxed max-w-md">
                        Sistem layanan publik terpadu untuk pengaduan, verifikasi, analisis kerusakan jalan (YOLO), sistem rekomendasi prioritas (TOPSIS), dan transparansi pemantauan progres perbaikan jalan dari minggu ke minggu.
                    </p>

                </div>

                <!-- Col 2: Navigasi -->
                <div>
                    <h3 class="text-white font-bold text-base mb-4 tracking-wide">Navigasi Utama</h3>
                    <ul class="space-y-2.5">
                        <li><a href="{{ route('home') }}" class="hover:text-amber-400 transition">Beranda</a></li>
                        <li><a href="{{ route('public.peta') }}" class="hover:text-amber-400 transition">Peta GIS Kerusakan</a></li>
                        <li><a href="{{ route('public.reports.index') }}" class="hover:text-amber-400 transition">Laporan Publik</a></li>
                        <li><a href="{{ route('public.statistik') }}" class="hover:text-amber-400 transition">Statistik & Analitik</a></li>
                        <li><a href="{{ route('public.cara-kerja') }}" class="hover:text-amber-400 transition">Alur Cara Kerja</a></li>
                    </ul>
                </div>

                <!-- Col 3: Kontak & Bantuan -->
                <div>
                    <h3 class="text-white font-bold text-base mb-4 tracking-wide">Pusat Bantuan</h3>
                    <ul class="space-y-3 text-sm">
                        <li class="flex items-center space-x-2.5">
                            <i class="fa-solid fa-phone text-amber-400"></i>
                            <span>Call Center: 112 / 1500-117</span>
                        </li>
                        <li class="flex items-center space-x-2.5">
                            <i class="fa-solid fa-envelope text-amber-400"></i>
                            <span>pengaduan@jalanku.go.id</span>
                        </li>
                        <li class="flex items-center space-x-2.5">
                            <i class="fa-solid fa-location-dot text-amber-400"></i>
                            <span>Pusat Layanan Infrastruktur Publik</span>
                        </li>
                    </ul>
                </div>

            </div>

            <div class="border-t border-slate-800 mt-12 pt-8 flex flex-col md:flex-row justify-between items-center text-xs text-slate-500 space-y-4 md:space-y-0">
                <p>&copy; {{ date('Y') }} <strong>{{ \App\Models\SystemSetting::appName() }}</strong>. Seluruh Hak Cipta Dilindungi. Sistem Layanan Publik Pemerintah.</p>
                <div class="flex space-x-6">
                    <a href="{{ route('public.tentang') }}" class="hover:text-slate-400">Kebijakan Privasi</a>
                    <a href="{{ route('public.cara-kerja') }}" class="hover:text-slate-400">Syarat & Ketentuan</a>
                    
                </div>
            </div>
        </div>
    </footer>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <!-- GLightbox JS -->
    <script src="https://cdn.jsdelivr.net/npm/glightbox/dist/js/glightbox.min.js"></script>

    <script>
        // Toggle mobile menu
                // Stable Dropdown with Click Toggle & Hover Delay
        const dropdownContainer = document.getElementById('user-dropdown-container');
        const dropdownBtn = document.getElementById('user-dropdown-btn');
        const dropdownMenu = document.getElementById('user-dropdown-menu');
        const dropdownIcon = document.getElementById('user-dropdown-icon');
        let closeTimeout = null;

        if (dropdownContainer && dropdownBtn && dropdownMenu) {
            // Click to toggle
            dropdownBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                const isHidden = dropdownMenu.classList.contains('hidden');
                if (isHidden) {
                    openDropdown();
                } else {
                    closeDropdown();
                }
            });

            // Hover to open with bridge
            dropdownContainer.addEventListener('mouseenter', () => {
                clearTimeout(closeTimeout);
                openDropdown();
            });

            dropdownContainer.addEventListener('mouseleave', () => {
                closeTimeout = setTimeout(() => {
                    closeDropdown();
                }, 400); // 400ms delay so it doesn't instantly close when moving mouse!
            });

            function openDropdown() {
                dropdownMenu.classList.remove('hidden');
                if (dropdownIcon) dropdownIcon.classList.add('rotate-180');
            }

            function closeDropdown() {
                dropdownMenu.classList.add('hidden');
                if (dropdownIcon) dropdownIcon.classList.remove('rotate-180');
            }

            // Close on click outside
            document.addEventListener('click', (e) => {
                if (!dropdownContainer.contains(e.target)) {
                    closeDropdown();
                }
            });
        }

        const menuBtn = document.getElementById('mobile-menu-button');
        const mobileMenu = document.getElementById('mobile-menu');
        if (menuBtn && mobileMenu) {
            menuBtn.addEventListener('click', () => {
                mobileMenu.classList.toggle('hidden');
            });
        }

        // Init GLightbox for all photos with class glightbox
        document.addEventListener('DOMContentLoaded', () => {
            if (typeof GLightbox !== 'undefined') {
                const lightbox = GLightbox({
                    selector: '.glightbox',
                    touchNavigation: true,
                    loop: true,
                });
            }
        });
    </script>

    @stack('scripts')
</body>
</html>
