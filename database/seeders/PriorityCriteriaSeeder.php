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
                'weight_percentage' => 40.00,
                'description' => 'Tingkat keparahan lubang (pothole), retakan (crack), atau longsor (landslide) serta estimasi luas jalan yang rusak berdasarkan deteksi AI YOLO.',
            ],
            [
                'code' => 'C2',
                'name' => 'Keselamatan Pengguna',
                'type' => 'benefit',
                'weight_percentage' => 25.00,
                'description' => 'Potensi bahaya kecelakaan, blind spot, dan risiko fatalitas bagi pengendara motor maupun mobil.',
            ],
            [
                'code' => 'C3',
                'name' => 'Jumlah Laporan Tervalidasi',
                'type' => 'benefit',
                'weight_percentage' => 25.00,
                'description' => 'Banyaknya aduan masyarakat yang terverifikasi pada ruas jalan yang sama atau berdekatan (Crowdsourcing).',
            ],
            [
                'code' => 'C4',
                'name' => 'Lama Belum Tertangani',
                'type' => 'benefit',
                'weight_percentage' => 10.00,
                'description' => 'Jumlah hari sejak laporan pertama diajukan hingga saat ini belum ditangani oleh dinas teknis.',
            ],
        ];

        PriorityCriterion::truncate();
        foreach ($criteria as $item) {
            PriorityCriterion::create($item);
        }
    }
}
