<?php

namespace Database\Seeders;

use App\Models\PriorityCriterion;
use Illuminate\Database\Seeder;

class PriorityCriteriaSeeder extends Seeder
{
    public function run(): void
    {
        $criteria = [
            [
                'code' => 'C1',
                'name' => 'Tingkat/Luas Kerusakan',
                'type' => 'benefit',
                'weight_percentage' => 35.00,
                'description' => 'Tingkat keparahan lubang, retakan, dan estimasi luas jalan yang rusak berdasarkan analisis foto AI YOLO.',
            ],
            [
                'code' => 'C2',
                'name' => 'Keselamatan Pengguna',
                'type' => 'benefit',
                'weight_percentage' => 20.00,
                'description' => 'Potensi bahaya kecelakaan, blind spot, dan risiko fatalitas bagi pengendara motor maupun mobil.',
            ],
            [
                'code' => 'C3',
                'name' => 'Jumlah Laporan Tervalidasi',
                'type' => 'benefit',
                'weight_percentage' => 20.00,
                'description' => 'Banyaknya aduan masyarakat yang terverifikasi pada ruas jalan yang sama / berdekatan (Crowdsourcing).',
            ],
            [
                'code' => 'C4',
                'name' => 'Fungsi / Kelas Jalan',
                'type' => 'benefit',
                'weight_percentage' => 15.00,
                'description' => 'Klasifikasi hierarki jalan (Jalan Nasional, Jalan Provinsi, Jalan Kabupaten, Jalan Poros Desa, Jalan Lingkungan).',
            ],
            [
                'code' => 'C5',
                'name' => 'Lama Belum Tertangani',
                'type' => 'benefit',
                'weight_percentage' => 10.00,
                'description' => 'Jumlah hari sejak laporan pertama diajukan hingga saat ini belum ditangani oleh dinas.',
            ],
        ];

        PriorityCriterion::truncate();
        foreach ($criteria as $item) {
            PriorityCriterion::create($item);
        }
    }
}
