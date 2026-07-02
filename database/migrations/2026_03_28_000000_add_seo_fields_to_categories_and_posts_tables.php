<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->string('seo_title')->nullable()->after('slug');
            $table->text('seo_description')->nullable()->after('seo_title');
        });

        Schema::table('posts', function (Blueprint $table) {
            $table->string('seo_title')->nullable()->after('slug');
            $table->text('seo_description')->nullable()->after('seo_title');
        });
    }

    public function down()
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn(['seo_title', 'seo_description']);
        });

        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn(['seo_title', 'seo_description']);
        });
    }
};
