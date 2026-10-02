<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'id' => 1,
                'name' => 'masyarakat',
                'display_name' => 'Masyarakat',
                'description' => 'Masyarakat pelapor dan pemantau kondisi perbaikan jalan.',
            ],
            [
                'id' => 2,
                'name' => 'admin',
                'display_name' => 'Admin Pengelola',
                'description' => 'Pengelola sistem, verifikator laporan, penugasan OPD, kriteria TOPSIS, dan konfigurasi master.',
            ],
            [
                'id' => 3,
                'name' => 'opd',
                'display_name' => 'OPD / Petugas Lapangan',
                'description' => 'Pelaksana survei, pekerjaan perbaikan jalan, dan dokumentasi progres mingguan.',
            ],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(['id' => $role['id']], $role);
        }

        Role::where('name', 'super_admin')->delete();
    }
}
