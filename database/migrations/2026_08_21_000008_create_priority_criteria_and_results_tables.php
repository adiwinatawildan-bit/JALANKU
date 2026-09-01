<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('priority_criteria', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // C1, C2, C3, C4, C5, C6, C7, C8
            $table->string('name');
            $table->enum('type', ['benefit', 'cost'])->default('benefit');
            $table->decimal('weight_percentage', 5, 2); // e.g. 25.00, 15.00
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('priority_weights', function (Blueprint $table) {
            $table->id();
            $table->foreignId('priority_criterion_id')->constrained('priority_criteria')->onDelete('cascade');
            $table->decimal('weight_percentage', 5, 2);
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('road_assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->unique()->constrained('reports')->onDelete('cascade');
            $table->decimal('c1_damage_scale', 5, 2)->default(3.00); // 1-5 Scale / YOLO detection
            $table->decimal('c2_user_safety', 5, 2)->default(3.00); // 1-5 Scale
            $table->decimal('c3_traffic_volume', 5, 2)->default(3.00); // 1-5 Scale
            $table->unsignedInteger('c4_report_count')->default(1); // Count of reports in same cluster
            $table->decimal('c5_road_function', 5, 2)->default(3.00); // 1-5 (Nasional=5, Provinsi=4, Kab=3, Poros=2, Lingk=1)
            $table->decimal('c6_facility_proximity', 5, 2)->default(3.00); // 1-5 Proximity to schools/hospitals/markets
            $table->decimal('c7_community_impact', 5, 2)->default(3.00); // 1-5 Scale
            $table->unsignedInteger('c8_pending_days')->default(0); // Days since submission
            $table->foreignId('evaluated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('priority_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->unique()->constrained('reports')->onDelete('cascade');
            $table->decimal('score', 8, 4); // TOPSIS preference score 0.0000 - 1.0000
            $table->unsignedInteger('rank')->default(1);
            $table->string('priority_level'); // 'Sangat Prioritas', 'Prioritas Tinggi', 'Sedang', 'Rendah'
            $table->text('reasoning')->nullable(); // Auto generated explanation text
            $table->json('calculation_details')->nullable(); // Detailed normalization matrix, D+, D-
            $table->timestamp('calculated_at');
            $table->timestamps();

            $table->index('score');
            $table->index('rank');
            $table->index('priority_level');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('priority_results');
        Schema::dropIfExists('road_assessments');
        Schema::dropIfExists('priority_weights');
        Schema::dropIfExists('priority_criteria');
    }
};
