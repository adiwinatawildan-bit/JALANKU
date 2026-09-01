@extends('layouts.admin')

@section('title', 'Kriteria & Bobot Prioritas TOPSIS - JALAN KU')
@section('header_title', 'Konfigurasi SPK: 8 Kriteria & Bobot TOPSIS')

@section('content')
<div class="space-y-8 max-w-5xl">
    
    <!-- Header Notice -->
    <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-sm space-y-3">
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center font-bold text-lg">
                <i class="fa-solid fa-scale-balanced"></i>
            </div>
            <div>
                <h2 class="text-xl font-extrabold text-navy-900">8 Kriteria Penentuan Prioritas Perbaikan Jalan</h2>
                <p class="text-xs text-slate-500">Bobot menentukan seberapa besar pengaruh setiap kriteria dalam perhitungan skor TOPSIS.</p>
            </div>
        </div>

        <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200 text-xs text-amber-950 flex items-start space-x-2.5">
            <i class="fa-solid fa-circle-info text-amber-600 text-base mt-0.5"></i>
            <div>
                <strong>Aturan Validasi Bobot:</strong> Total seluruh bobot kriteria harus tepat bernilai <strong>100.00%</strong>. Setiap perubahan bobot akan secara otomatis memicu kalkulasi ulang skor preferensi seluruh pengaduan aktif.
            </div>
        </div>
    </div>

    <!-- 23. FORM ATUR BOBOT TOPSIS -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 sm:p-8 space-y-6">
        <form method="POST" action="{{ route('superadmin.criteria.update-weights') }}" id="weights-form" class="space-y-6">
            @csrf

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-700">
                    <thead class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider border-b border-slate-200">
                        <tr>
                            <th class="py-3 px-3 text-center">Kode</th>
                            <th class="py-3 px-3">Nama Kriteria</th>
                            <th class="py-3 px-3">Tipe Atribut</th>
                            <th class="py-3 px-3">Deskripsi & Indikator Penilaian</th>
                            <th class="py-3 px-3 text-right" style="width: 140px;">Bobot (%)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        @foreach($criteria as $crit)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-4 px-3 text-center font-mono font-bold text-purple-700 bg-purple-50/50 rounded-lg">
                                    {{ $crit->code }}
                                </td>
                                <td class="py-4 px-3 font-bold text-navy-900">
                                    {{ $crit->name }}
                                </td>
                                <td class="py-4 px-3">
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 uppercase">
                                        {{ $crit->type }}
                                    </span>
                                </td>
                                <td class="py-4 px-3 text-slate-500 text-[11px] max-w-sm">
                                    {{ $crit->description }}
                                </td>
                                <td class="py-4 px-3 text-right">
                                    <div class="relative flex items-center justify-end">
                                        <input type="number" step="0.01" min="0" max="100" name="weights[{{ $crit->code }}]" value="{{ $crit->weight_percentage }}" class="weight-input w-24 px-3 py-1.5 bg-slate-50 border border-slate-300 rounded-xl text-right font-bold text-xs focus:ring-2 focus:ring-amber-500 focus:outline-none" required>
                                        <span class="ml-1.5 font-bold text-slate-400">%</span>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-slate-50 border-t-2 border-slate-200 font-extrabold text-sm">
                        <tr>
                            <td colspan="4" class="py-4 px-4 text-right text-navy-900">
                                TOTAL AKUMULASI BOBOT:
                            </td>
                            <td class="py-4 px-3 text-right">
                                <span id="total-weight-display" class="font-mono text-base text-purple-700">
                                    {{ number_format($totalWeight, 2) }}%
                                </span>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div id="weight-alert" class="hidden p-3 rounded-2xl bg-rose-50 border border-rose-200 text-xs font-bold text-rose-700 text-center">
                ⚠️ Total bobot harus tepat 100.00%. Silakan periksa kembali angka yang Anda masukkan.
            </div>

            <div class="flex justify-end pt-4 border-t border-slate-100">
                <button type="submit" id="btn-save-weights" class="px-8 py-3 bg-navy-900 hover:bg-amber-600 text-white font-bold text-xs rounded-xl shadow-lg transition flex items-center space-x-2">
                    <i class="fa-solid fa-floppy-disk text-amber-400"></i>
                    <span>Simpan Bobot & Hitung Ulang Peringkat</span>
                </button>
            </div>

        </form>
    </div>

</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var inputs = document.querySelectorAll('.weight-input');
        var display = document.getElementById('total-weight-display');
        var alertBox = document.getElementById('weight-alert');
        var saveBtn = document.getElementById('btn-save-weights');

        function recalculateTotal() {
            var sum = 0;
            inputs.forEach(function(input) {
                sum += parseFloat(input.value) || 0;
            });

            display.innerText = sum.toFixed(2) + '%';

            if (Math.abs(sum - 100.0) > 0.01) {
                display.classList.remove('text-purple-700', 'text-emerald-600');
                display.classList.add('text-rose-600');
                alertBox.classList.remove('hidden');
                saveBtn.classList.add('opacity-50', 'cursor-not-allowed');
            } else {
                display.classList.remove('text-rose-600');
                display.classList.add('text-emerald-600');
                alertBox.classList.add('hidden');
                saveBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            }
        }

        inputs.forEach(function(input) {
            input.addEventListener('input', recalculateTotal);
        });

        recalculateTotal();
    });
</script>
@endpush
