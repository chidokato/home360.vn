<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->string('footer_column_1_title')->nullable()->after('favicon');
            $table->longText('footer_column_1_content')->nullable()->after('footer_column_1_title');
            $table->string('footer_column_2_title')->nullable()->after('footer_column_1_content');
            $table->longText('footer_column_2_content')->nullable()->after('footer_column_2_title');
            $table->string('footer_column_3_title')->nullable()->after('footer_column_2_content');
            $table->longText('footer_column_3_content')->nullable()->after('footer_column_3_title');
            $table->string('footer_column_4_title')->nullable()->after('footer_column_3_content');
            $table->longText('footer_column_4_content')->nullable()->after('footer_column_4_title');
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn([
                'footer_column_1_title',
                'footer_column_1_content',
                'footer_column_2_title',
                'footer_column_2_content',
                'footer_column_3_title',
                'footer_column_3_content',
                'footer_column_4_title',
                'footer_column_4_content',
            ]);
        });
    }
};
