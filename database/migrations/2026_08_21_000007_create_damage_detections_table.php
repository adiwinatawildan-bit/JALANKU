<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('damage_detections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->constrained('reports')->onDelete('cascade');
            $table->foreignId('report_photo_id')->nullable()->constrained('report_photos')->onDelete('cascade');
            $table->json('detected_classes')->nullable(); // e.g. {"pothole": 7, "crack": 4}
            $table->unsignedInteger('total_defects')->default(0);
            $table->decimal('confidence_score', 5, 2)->default(0);
            $table->json('bounding_boxes')->nullable();
            $table->decimal('damaged_area_sqm', 8, 2)->nullable();
            $table->string('annotated_image_path')->nullable();
            $table->text('annotated_image_url')->nullable();
            $table->string('model_version')->default('YOLOv8-RoadDamage');
            $table->timestamps();

            $table->index('report_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('damage_detections');
    }
};
