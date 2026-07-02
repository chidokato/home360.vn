<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('settings')) {
            return;
        }

        $columns = [
            'home_seo_title',
            'home_seo_description',
            'home_seo_keywords',
            'about_seo_title',
            'about_seo_description',
            'about_seo_keywords',
            'contact_seo_title',
            'contact_seo_description',
            'contact_seo_keywords',
        ];

        $existingColumns = array_values(array_filter($columns, fn ($column) => Schema::hasColumn('settings', $column)));

        if ($existingColumns === []) {
            return;
        }

        Schema::table('settings', function (Blueprint $table) use ($existingColumns) {
            $table->dropColumn($existingColumns);
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('settings')) {
            return;
        }

        Schema::table('settings', function (Blueprint $table) {
            if (! Schema::hasColumn('settings', 'home_seo_title')) {
                $table->string('home_seo_title')->nullable();
            }

            if (! Schema::hasColumn('settings', 'home_seo_description')) {
                $table->text('home_seo_description')->nullable();
            }

            if (! Schema::hasColumn('settings', 'home_seo_keywords')) {
                $table->text('home_seo_keywords')->nullable();
            }

            if (! Schema::hasColumn('settings', 'about_seo_title')) {
                $table->string('about_seo_title')->nullable();
            }

            if (! Schema::hasColumn('settings', 'about_seo_description')) {
                $table->text('about_seo_description')->nullable();
            }

            if (! Schema::hasColumn('settings', 'about_seo_keywords')) {
                $table->text('about_seo_keywords')->nullable();
            }

            if (! Schema::hasColumn('settings', 'contact_seo_title')) {
                $table->string('contact_seo_title')->nullable();
            }

            if (! Schema::hasColumn('settings', 'contact_seo_description')) {
                $table->text('contact_seo_description')->nullable();
            }

            if (! Schema::hasColumn('settings', 'contact_seo_keywords')) {
                $table->text('contact_seo_keywords')->nullable();
            }
        });
    }
};
