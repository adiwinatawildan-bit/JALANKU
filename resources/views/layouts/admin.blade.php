<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Portal - ' . \App\Models\SystemSetting::appName())</title>

    <!-- Google Fonts -->
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

    <!-- ApexCharts CDN -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">

    @stack('styles')
</head>
<body class="bg-slate-100 text-slate-800 antialiased min-h-screen flex flex-col font-sans">

    <!-- Mobile Sidebar Overlay & Drawer -->
    <div id="mobile-sidebar-overlay" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-40 hidden md:hidden transition-opacity duration-300 opacity-0"></div>
    <aside id="mobile-sidebar-drawer" class="fixed top-0 left-0 bottom-0 w-72 bg-navy-950 text-slate-300 z-50 flex flex-col transform -translate-x-full transition-transform duration-300 ease-in-out md:hidden overflow-y-auto">
        <!-- Drawer Header -->
        <div class="h-16 flex items-center justify-between px-5 border-b border-slate-800 shrink-0">
            <a href="{{ route('home') }}" class="flex items-center space-x-2.5">
                <img src="{{ \App\Models\SystemSetting::getLogo() }}" alt="{{ \App\Models\SystemSetting::appName() }} Logo" class="w-9 h-9 rounded-xl object-contain bg-navy-900 border border-slate-700/80 p-0.5 shadow-md shadow-amber-500/10">
                <div>
                    <span class="text-base font-extrabold text-white tracking-tight">{{ \App\Models\SystemSetting::appName() }}</span>
                    <p class="text-[9px] text-slate-400 font-semibold tracking-wider uppercase">Portal Manajemen</p>
                </div>
            </a>
            <button id="mobile-sidebar-close" class="w-8 h-8 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-400 hover:text-white flex items-center justify-center transition">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>

        <!-- Drawer User Badge -->
        <div class="px-5 py-3 border-b border-slate-800/80 bg-slate-900/50 shrink-0">
            <div class="flex items-center space-x-3">
                @if(Auth::user()->getAvatar())
                    <img src="{{ Auth::user()->getAvatar() }}" alt="{{ Auth::user()->name }}" class="w-8 h-8 rounded-full object-cover border border-amber-400">
                @else
                    <div class="w-8 h-8 rounded-full bg-slate-800 border border-slate-700 flex items-center justify-center text-amber-400 font-bold text-sm">
                        <i class="fa-solid fa-user-shield"></i>
                    </div>
                @endif
                <div class="overflow-hidden">
                    <p class="text-xs font-bold text-white truncate">{{ Auth::user()->name }}</p>
                    <span class="inline-block px-2 py-0.5 rounded text-[9px] font-bold bg-amber-500/20 text-amber-400 uppercase tracking-wider">
                        {{ Auth::user()->role->display_name }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Drawer Navigation Links -->
        <div class="flex-1 overflow-y-auto py-3 px-3 space-y-5">
            @if(Auth::user()->isAdmin())
                <div>
                    <p class="px-3 text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1.5">Menu Admin</p>
                    <nav class="space-y-0.5">
                        <a href="{{ route('admin.dashboard') }}" class="flex items-center px-3 py-2 rounded-xl text-xs font-semibold transition {{ request()->routeIs('admin.dashboard') ? 'bg-amber-500 text-navy-950 font-bold shadow' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <i class="fa-solid fa-chart-pie w-5 mr-2.5 {{ request()->routeIs('admin.dashboard') ? 'text-navy-950' : 'text-amber-400' }}"></i>
                            Dashboard Admin
                        </a>
                        <a href="{{ route('admin.reports.index') }}" class="flex items-center px-3 py-2 rounded-xl text-xs font-semibold transition {{ request()->routeIs('admin.reports.*') ? 'bg-amber-500 text-navy-950 font-bold shadow' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <i class="fa-solid fa-list-check w-5 mr-2.5 {{ request()->routeIs('admin.reports.*') ? 'text-navy-950' : 'text-sky-400' }}"></i>
                            Kelola Laporan
                        </a>
                        <a href="{{ route('public.peta') }}" target="_blank" class="flex items-center px-3 py-2 rounded-xl text-xs font-semibold text-slate-300 hover:bg-slate-800 hover:text-white transition">
                            <i class="fa-solid fa-map-location-dot w-5 mr-2.5 text-emerald-400"></i>
                            Peta GIS Jalan
                        </a>
                        <a href="{{ route('admin.audit-logs') }}" class="flex items-center px-3 py-2 rounded-xl text-xs font-semibold transition {{ request()->routeIs('admin.audit-logs') ? 'bg-amber-500 text-navy-950 font-bold shadow' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <i class="fa-solid fa-clock-rotate-left w-5 mr-2.5 {{ request()->routeIs('admin.audit-logs') ? 'text-navy-950' : 'text-indigo-400' }}"></i>
                            Audit Log
                        </a>
                    </nav>
                </div>
            @endif

            @if(Auth::user()->isOpd())
                <div>
                    <p class="px-3 text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1.5">Menu Tugas OPD</p>
                    <nav class="space-y-0.5">
                        <a href="{{ route('opd.dashboard') }}" class="flex items-center px-3 py-2 rounded-xl text-xs font-semibold transition {{ request()->routeIs('opd.dashboard') ? 'bg-amber-500 text-navy-950 font-bold shadow' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <i class="fa-solid fa-chart-line w-5 mr-2.5 {{ request()->routeIs('opd.dashboard') ? 'text-navy-950' : 'text-amber-400' }}"></i>
                            Dashboard OPD
                        </a>
                        <a href="{{ route('opd.tasks.index') }}" class="flex items-center px-3 py-2 rounded-xl text-xs font-semibold transition {{ request()->routeIs('opd.tasks.*') ? 'bg-amber-500 text-navy-950 font-bold shadow' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <i class="fa-solid fa-helmet-safety w-5 mr-2.5 {{ request()->routeIs('opd.tasks.*') ? 'text-navy-950' : 'text-orange-400' }}"></i>
                            Tugas Perbaikan
                        </a>
                    </nav>
                </div>
            @endif

            @if(Auth::user()->isSuperAdmin())
                <div>
                    <p class="px-3 text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1.5">Super Admin</p>
                    <nav class="space-y-0.5">
                        <a href="{{ route('superadmin.dashboard') }}" class="flex items-center px-3 py-2 rounded-xl text-xs font-semibold transition {{ request()->routeIs('superadmin.dashboard') ? 'bg-amber-500 text-navy-950 font-bold shadow' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <i class="fa-solid fa-sliders w-5 mr-2.5 {{ request()->routeIs('superadmin.dashboard') ? 'text-navy-950' : 'text-amber-400' }}"></i>
                            Dashboard
                        </a>
                        <a href="{{ route('superadmin.users.index') }}" class="flex items-center px-3 py-2 rounded-xl text-xs font-semibold transition {{ request()->routeIs('superadmin.users.*') ? 'bg-amber-500 text-navy-950 font-bold shadow' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <i class="fa-solid fa-users w-5 mr-2.5 text-sky-400"></i>
                            User Management
                        </a>
                        <a href="{{ route('superadmin.opds.index') }}" class="flex items-center px-3 py-2 rounded-xl text-xs font-semibold transition {{ request()->routeIs('superadmin.opds.*') ? 'bg-amber-500 text-navy-950 font-bold shadow' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <i class="fa-solid fa-building-columns w-5 mr-2.5 text-emerald-400"></i>
                            Kelola OPD
                        </a>
                        <a href="{{ route('superadmin.criteria.index') }}" class="flex items-center px-3 py-2 rounded-xl text-xs font-semibold transition {{ request()->routeIs('superadmin.criteria.*') ? 'bg-amber-500 text-navy-950 font-bold shadow' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <i class="fa-solid fa-scale-balanced w-5 mr-2.5 text-purple-400"></i>
                            Kriteria TOPSIS
                        </a>
                        <a href="{{ route('superadmin.audit-logs.index') }}" class="flex items-center px-3 py-2 rounded-xl text-xs font-semibold transition {{ request()->routeIs('superadmin.audit-logs.*') ? 'bg-amber-500 text-navy-950 font-bold shadow' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <i class="fa-solid fa-clipboard-list w-5 mr-2.5 text-indigo-400"></i>
                            Audit Logs
                        </a>
                        <a href="{{ route('superadmin.settings.index') }}" class="flex items-center px-3 py-2 rounded-xl text-xs font-semibold transition {{ request()->routeIs('superadmin.settings.*') ? 'bg-amber-500 text-navy-950 font-bold shadow' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <i class="fa-solid fa-gear w-5 mr-2.5 text-slate-400"></i>
                            Konfigurasi
                        </a>
                    </nav>
                </div>
            @endif

            <div class="pt-3 border-t border-slate-800">
                <a href="{{ route('home') }}" class="flex items-center px-3 py-2 rounded-xl text-xs font-semibold text-slate-400 hover:bg-slate-800 hover:text-white transition">
                    <i class="fa-solid fa-arrow-up-right-from-square w-5 mr-2.5 text-amber-400"></i>
                    Portal Publik
                </a>
            </div>
        </div>

        <!-- Drawer Logout -->
        <div class="p-3 border-t border-slate-800 shrink-0">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center justify-center space-x-2 px-3 py-2 rounded-xl bg-slate-900 hover:bg-rose-950/60 text-rose-400 text-xs font-semibold border border-slate-800 transition">
                    <i class="fa-solid fa-arrow-right-from-bracket"></i>
                    <span>Keluar (Logout)</span>
                </button>
            </form>
        </div>
    </aside>

    <div class="flex flex-1 min-h-screen">
        
        <!-- Sidebar Navigation (Desktop) -->
        <aside class="w-64 bg-navy-950 text-slate-300 border-r border-slate-800 flex flex-col shrink-0 hidden md:flex">
            <!-- Brand -->
            <div class="h-20 flex items-center px-6 border-b border-slate-800">
                <a href="{{ route('home') }}" class="flex items-center space-x-3">
                    <img src="{{ \App\Models\SystemSetting::getLogo() }}" alt="{{ \App\Models\SystemSetting::appName() }} Logo" class="w-10 h-10 rounded-xl object-contain bg-navy-900 border border-slate-700/80 p-0.5 shadow-md shadow-amber-500/10">
                    <div>
                        <span class="text-lg font-extrabold text-white tracking-tight">{{ \App\Models\SystemSetting::appName() }}</span>
                        <p class="text-[10px] text-slate-400 font-semibold tracking-wider uppercase">Portal Manajemen</p>
                    </div>
                </a>
            </div>

            <!-- Role Badge -->
            <div class="px-6 py-4 border-b border-slate-800/80 bg-slate-900/50">
                <div class="flex items-center space-x-3">
                    @if(Auth::user()->getAvatar())
                        <img src="{{ Auth::user()->getAvatar() }}" alt="{{ Auth::user()->name }}" class="w-9 h-9 rounded-full object-cover border border-amber-400">
                    @else
                        <div class="w-9 h-9 rounded-full bg-slate-800 border border-slate-700 flex items-center justify-center text-amber-400 font-bold">
                            <i class="fa-solid fa-user-shield"></i>
                        </div>
                    @endif
                    <div class="overflow-hidden">
                        <p class="text-xs font-bold text-white truncate">{{ Auth::user()->name }}</p>
                        <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold bg-amber-500/20 text-amber-400 uppercase tracking-wider">
                            {{ Auth::user()->role->display_name }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Navigation Links -->
            <div class="flex-1 overflow-y-auto py-4 px-3 space-y-6">
                
                @if(Auth::user()->isAdmin())
                    <!-- Admin Navigation -->
                    <div>
                        <p class="px-3 text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-2">Menu Pengelola Admin</p>
                        <nav class="space-y-1">
                            <a href="{{ route('admin.dashboard') }}" class="flex items-center px-3.5 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('admin.dashboard') ? 'bg-amber-500 text-navy-950 font-bold shadow' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                                <i class="fa-solid fa-chart-pie w-5 mr-3 {{ request()->routeIs('admin.dashboard') ? 'text-navy-950' : 'text-amber-400' }}"></i>
                                Dashboard Admin
                            </a>
                            <a href="{{ route('admin.reports.index') }}" class="flex items-center px-3.5 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('admin.reports.*') ? 'bg-amber-500 text-navy-950 font-bold shadow' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                                <i class="fa-solid fa-list-check w-5 mr-3 {{ request()->routeIs('admin.reports.*') ? 'text-navy-950' : 'text-sky-400' }}"></i>
                                Kelola Seluruh Laporan
                            </a>
                            <a href="{{ route('public.peta') }}" target="_blank" class="flex items-center px-3.5 py-2.5 rounded-xl text-sm font-medium text-slate-300 hover:bg-slate-800 hover:text-white transition">
                                <i class="fa-solid fa-map-location-dot w-5 mr-3 text-emerald-400"></i>
                                Peta GIS Jalan
                            </a>
                            <a href="{{ route('admin.audit-logs') }}" class="flex items-center px-3.5 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('admin.audit-logs') ? 'bg-amber-500 text-navy-950 font-bold shadow' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                                <i class="fa-solid fa-clock-rotate-left w-5 mr-3 {{ request()->routeIs('admin.audit-logs') ? 'text-navy-950' : 'text-indigo-400' }}"></i>
                                Audit Log
                            </a>
                        </nav>
                    </div>
                @endif

                @if(Auth::user()->isOpd())
                    <!-- OPD Navigation -->
                    <div>
                        <p class="px-3 text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-2">Menu Tugas Lapangan / OPD</p>
                        <nav class="space-y-1">
                            <a href="{{ route('opd.dashboard') }}" class="flex items-center px-3.5 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('opd.dashboard') ? 'bg-amber-500 text-navy-950 font-bold shadow' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                                <i class="fa-solid fa-chart-line w-5 mr-3 {{ request()->routeIs('opd.dashboard') ? 'text-navy-950' : 'text-amber-400' }}"></i>
                                Dashboard OPD
                            </a>
                            <a href="{{ route('opd.tasks.index') }}" class="flex items-center px-3.5 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('opd.tasks.*') ? 'bg-amber-500 text-navy-950 font-bold shadow' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                                <i class="fa-solid fa-helmet-safety w-5 mr-3 {{ request()->routeIs('opd.tasks.*') ? 'text-navy-950' : 'text-orange-400' }}"></i>
                                Daftar Tugas Perbaikan
                            </a>
                        </nav>
                    </div>
                @endif

                @if(Auth::user()->isSuperAdmin())
                    <!-- Super Admin Navigation -->
                    <div>
                        <p class="px-3 text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-2">Master & Konfigurasi Super Admin</p>
                        <nav class="space-y-1">
                            <a href="{{ route('superadmin.dashboard') }}" class="flex items-center px-3.5 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('superadmin.dashboard') ? 'bg-amber-500 text-navy-950 font-bold shadow' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                                <i class="fa-solid fa-sliders w-5 mr-3 {{ request()->routeIs('superadmin.dashboard') ? 'text-navy-950' : 'text-amber-400' }}"></i>
                                Dashboard Super Admin
                            </a>
                            <a href="{{ route('superadmin.users.index') }}" class="flex items-center px-3.5 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('superadmin.users.*') ? 'bg-amber-500 text-navy-950 font-bold shadow' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                                <i class="fa-solid fa-users w-5 mr-3 text-sky-400"></i>
                                User Management
                            </a>
                            <a href="{{ route('superadmin.opds.index') }}" class="flex items-center px-3.5 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('superadmin.opds.*') ? 'bg-amber-500 text-navy-950 font-bold shadow' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                                <i class="fa-solid fa-building-columns w-5 mr-3 text-emerald-400"></i>
                                Kelola OPD
                            </a>
                            <a href="{{ route('superadmin.criteria.index') }}" class="flex items-center px-3.5 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('superadmin.criteria.*') ? 'bg-amber-500 text-navy-950 font-bold shadow' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                                <i class="fa-solid fa-scale-balanced w-5 mr-3 text-purple-400"></i>
                                Kriteria & Bobot TOPSIS
                            </a>

                            <a href="{{ route('superadmin.audit-logs.index') }}" class="flex items-center px-3.5 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('superadmin.audit-logs.*') ? 'bg-amber-500 text-navy-950 font-bold shadow' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                                <i class="fa-solid fa-clipboard-list w-5 mr-3 text-indigo-400"></i>
                                Audit Logs Lengkap
                            </a>
                            <a href="{{ route('superadmin.settings.index') }}" class="flex items-center px-3.5 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('superadmin.settings.*') ? 'bg-amber-500 text-navy-950 font-bold shadow' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                                <i class="fa-solid fa-gear w-5 mr-3 text-slate-400"></i>
                                Konfigurasi Sistem
                            </a>
                        </nav>
                    </div>
                @endif

                <!-- Portal Publik Shortcut -->
                <div class="pt-4 border-t border-slate-800">
                    <a href="{{ route('home') }}" class="flex items-center px-3.5 py-2 rounded-xl text-sm font-medium text-slate-400 hover:bg-slate-800 hover:text-white transition">
                        <i class="fa-solid fa-arrow-up-right-from-square w-5 mr-3 text-amber-400"></i>
                        Lihat Portal Publik
                    </a>
                </div>

            </div>

            <!-- Bottom User & Logout -->
            <div class="p-4 border-t border-slate-800">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center justify-center space-x-2 px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-rose-950/60 text-rose-400 text-sm font-semibold border border-slate-800 transition">
                        <i class="fa-solid fa-arrow-right-from-bracket"></i>
                        <span>Keluar (Logout)</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col overflow-x-hidden">
            
            <!-- Top Header -->
            <header class="h-16 md:h-20 bg-white border-b border-slate-200 flex items-center justify-between px-4 sm:px-6 md:px-8 z-10">
                <div class="flex items-center space-x-3">
                    <!-- Mobile Hamburger Button -->
                    <button id="mobile-sidebar-toggle" class="md:hidden w-10 h-10 rounded-xl bg-navy-900 hover:bg-navy-800 text-amber-400 flex items-center justify-center transition active:scale-95">
                        <i class="fa-solid fa-bars text-base"></i>
                    </button>
                    <h1 class="text-base md:text-xl font-bold text-slate-900 truncate">@yield('header_title', 'Dashboard')</h1>
                </div>

                <div class="flex items-center space-x-3 sm:space-x-4">
                    <div class="flex items-center space-x-3 pl-3 border-l border-slate-200">
                        <div class="text-right hidden sm:block">
                            <p class="text-xs font-bold text-slate-900">{{ Auth::user()->name }}</p>
                            <p class="text-[11px] text-slate-500">{{ Auth::user()->role->display_name }}</p>
                        </div>
                        <a href="{{ route('profile') }}" class="w-10 h-10 rounded-full bg-navy-900 text-amber-400 flex items-center justify-center font-bold overflow-hidden border border-slate-200 hover:ring-2 hover:ring-amber-500 transition">
                            @if(Auth::user()->getAvatar())
                                <img src="{{ Auth::user()->getAvatar() }}" alt="{{ Auth::user()->name }}" class="w-full h-full object-cover">
                            @else
                                <i class="fa-solid fa-user"></i>
                            @endif
                        </a>
                    </div>
                </div>
            </header>

            <!-- Main Body -->
            <main class="flex-1 p-6 sm:p-8 bg-slate-50">
                <!-- Alerts -->
                @if(session('success'))
                    <div class="mb-6 flex items-start p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-900 shadow-sm" role="alert">
                        <i class="fa-solid fa-circle-check text-emerald-600 text-xl mr-3 mt-0.5"></i>
                        <div class="flex-1">
                            <p class="font-bold text-sm">Berhasil!</p>
                            <p class="text-sm text-emerald-800">{{ session('success') }}</p>
                        </div>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-6 flex items-start p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-900 shadow-sm" role="alert">
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
                            <h4 class="font-bold text-sm">Periksa kembali input form Anda:</h4>
                        </div>
                        <ul class="list-disc list-inside text-sm text-amber-800 space-y-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')
            </main>

        </div>

    </div>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <!-- GLightbox JS -->
    <script src="https://cdn.jsdelivr.net/npm/glightbox/dist/js/glightbox.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (typeof GLightbox !== 'undefined') {
                GLightbox({ selector: '.glightbox' });
            }
        });
    </script>

    <!-- Mobile Sidebar Drawer Toggle Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const overlay = document.getElementById('mobile-sidebar-overlay');
            const drawer = document.getElementById('mobile-sidebar-drawer');
            const toggleBtn = document.getElementById('mobile-sidebar-toggle');
            const closeBtn = document.getElementById('mobile-sidebar-close');

            function openDrawer() {
                overlay.classList.remove('hidden');
                requestAnimationFrame(() => {
                    overlay.classList.remove('opacity-0');
                    overlay.classList.add('opacity-100');
                    drawer.classList.remove('-translate-x-full');
                    drawer.classList.add('translate-x-0');
                });
            }

            function closeDrawer() {
                overlay.classList.remove('opacity-100');
                overlay.classList.add('opacity-0');
                drawer.classList.remove('translate-x-0');
                drawer.classList.add('-translate-x-full');
                setTimeout(() => { overlay.classList.add('hidden'); }, 300);
            }

            if (toggleBtn) toggleBtn.addEventListener('click', openDrawer);
            if (closeBtn) closeBtn.addEventListener('click', closeDrawer);
            if (overlay) overlay.addEventListener('click', closeDrawer);
        });
    </script>

    @stack('scripts')
</body>
</html>
