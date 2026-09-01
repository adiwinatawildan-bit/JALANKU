@extends('layouts.admin')

@section('title', 'Hak Akses & Role Sistem - JALAN KU')
@section('header_title', 'Peran & Hak Akses (RBAC)')

@section('content')
<div class="space-y-6 max-w-5xl">
    
    <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-sm space-y-4">
        <h2 class="text-xl font-extrabold text-navy-900">Struktur Peran Pengguna (4 Role)</h2>
        <p class="text-xs text-slate-600 leading-relaxed">
            Sistem JALAN KU mengimplementasikan 4 tingkatan hak akses berbasis Role-Based Access Control (RBAC) untuk memisahkan kewenangan masyarakat pelapor, pengawas admin, pelaksana teknis OPD, dan administrator sistem.
        </p>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-4">
            @foreach($roles as $role)
                <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200 space-y-2">
                    <div class="flex justify-between items-center">
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-navy-900 text-amber-400 font-mono">
                            {{ $role->name }}
                        </span>
                        <span class="text-xs font-bold text-slate-500">{{ $role->users_count }} Pengguna</span>
                    </div>
                    <h3 class="text-base font-bold text-navy-900">{{ $role->display_name }}</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">{{ $role->description }}</p>
                </div>
            @endforeach
        </div>
    </div>

</div>
@endsection
