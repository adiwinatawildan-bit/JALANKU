<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number')->unique(); // e.g. JLK-202608-0001
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('opd_id')->nullable()->constrained('opds')->nullOnDelete();
            $table->string('title');
            $table->text('description');
            $table->string('road_name');
            $table->string('kecamatan');
            $table->string('desa');
            $table->string('damage_type')->default('pothole'); // pothole, retak, amblas, bergelombang, drainase, lainnya
            $table->string('disturbance_level')->default('sedang'); // rendah, sedang, tinggi, sangat_parah
            $table->text('additional_info')->nullable();
            $table->string('status')->default('DIAJUKAN'); 
            // Status flow: DIAJUKAN, DIVERIFIKASI, DITUGASKAN, SURVEI, MENUNGGU PERBAIKAN, SEDANG DIPERBAIKI, SELESAI, DITOLAK, DUPLIKAT
            
            $table->text('rejection_reason')->nullable();
            $table->foreignId('duplicate_of_id')->nullable()->constrained('reports')->nullOnDelete();
            $table->string('cluster_id')->nullable()->index();
            $table->boolean('is_public')->default(true);

            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->foreignId('assigned_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('assigned_at')->nullable();
            
            $table->text('survey_notes')->nullable();
            $table->timestamp('survey_at')->nullable();
            $table->timestamp('completed_at')->nullable();

            $table->text('citizen_feedback')->nullable();
            $table->unsignedTinyInteger('citizen_rating')->nullable(); // 1 to 5

            $table->timestamps();

            $table->index('status');
            $table->index(['kecamatan', 'desa']);
            $table->index('created_at');
        });

        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->constrained('reports')->onDelete('cascade');
            $table->string('road_name');
            $table->text('address_detail')->nullable();
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->string('kecamatan');
            $table->string('desa');
            $table->string('postal_code')->nullable();
            $table->timestamps();

            $table->index(['latitude', 'longitude']);
            $table->index(['kecamatan', 'desa']);
        });

        Schema::create('report_status_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->constrained('reports')->onDelete('cascade');
            $table->string('from_status')->nullable();
            $table->string('to_status');
            $table->text('notes')->nullable();
            $table->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['report_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report_status_histories');
        Schema::dropIfExists('locations');
        Schema::dropIfExists('reports');
    }
};
