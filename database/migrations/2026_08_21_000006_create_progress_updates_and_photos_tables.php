<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('progress_updates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->constrained('reports')->onDelete('cascade');
            $table->unsignedInteger('week_number'); // 1, 2, 3, 4, ...
            $table->date('date');
            $table->string('status')->default('SEDANG DIPERBAIKI');
            $table->decimal('progress_percentage', 5, 2); // 20.00, 50.00, 80.00, 100.00
            $table->text('description');
            $table->foreignId('uploaded_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();

            $table->index(['report_id', 'week_number']);
        });

        Schema::create('progress_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('progress_update_id')->constrained('progress_updates')->onDelete('cascade');
            $table->string('file_name');
            $table->string('file_path');
            $table->text('file_url');
            $table->string('caption')->nullable();
            $table->foreignId('uploaded_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();

            $table->index('progress_update_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('progress_photos');
        Schema::dropIfExists('progress_updates');
    }
};
