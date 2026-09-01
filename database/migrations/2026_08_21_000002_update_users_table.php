<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('role_id')->after('id')->default(1)->constrained('roles')->onDelete('restrict');
            $table->foreignId('opd_id')->nullable()->after('role_id')->constrained('opds')->nullOnDelete();
            $table->string('phone')->nullable()->after('email');
            $table->string('avatar_url')->nullable()->after('password');
            $table->boolean('is_active')->default(true)->after('avatar_url');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropForeign(['opd_id']);
            $table->dropColumn(['role_id', 'opd_id', 'phone', 'avatar_url', 'is_active']);
        });
    }
};
