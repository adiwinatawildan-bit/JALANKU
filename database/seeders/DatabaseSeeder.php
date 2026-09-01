<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            OpdSeeder::class,
            UserSeeder::class,
            PriorityCriteriaSeeder::class,
            // ReportSeeder::class, // Commented out for fresh production/testing usage
        ]);
    }
}
