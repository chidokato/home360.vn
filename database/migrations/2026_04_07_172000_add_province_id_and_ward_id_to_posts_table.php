<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->foreignId('province_id')->nullable()->after('address')->constrained('provinces')->nullOnDelete();
            $table->foreignId('ward_id')->nullable()->after('province_id')->constrained('wards')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropConstrainedForeignId('ward_id');
            $table->dropConstrainedForeignId('province_id');
        });
    }
};
