<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('facilities', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type'); // sekolah, rumah_sakit, pasar, terminal, tempat_ibadah, kantor_pemerintah, dll
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->string('kecamatan')->nullable();
            $table->string('desa')->nullable();
            $table->text('address')->nullable();
            $table->timestamps();

            $table->index(['latitude', 'longitude']);
            $table->index(['kecamatan', 'desa']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('facilities');
    }
};
