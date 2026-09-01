<?php

namespace Database\Seeders;

use App\Models\Opd;
use Illuminate\Database\Seeder;

class OpdSeeder extends Seeder
{
    public function run(): void
    {
        $opds = [
            [
                'id' => 1,
                'name' => 'Dinas Pekerjaan Umum dan Penataan Ruang',
                'code' => 'DPUPR',
                'phone' => '0262-232123',
                'email' => 'dpupr@jalanku.go.id',
                'address' => 'Jl. Raya Samarang No. 115',
                'is_active' => true,
            ],
        ];

        foreach ($opds as $opd) {
            Opd::updateOrCreate(['id' => $opd['id']], $opd);
        }
    }
}
