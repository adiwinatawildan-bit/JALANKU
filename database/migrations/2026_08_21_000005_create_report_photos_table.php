<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('report_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->constrained('reports')->onDelete('cascade');
            $table->string('file_name');
            $table->string('file_path');
            $table->text('file_url');
            $table->string('photo_type')->default('initial'); // 'initial', 'survey'
            $table->string('caption')->nullable();
            $table->foreignId('uploaded_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();

            $table->index(['report_id', 'photo_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report_photos');
    }
};
