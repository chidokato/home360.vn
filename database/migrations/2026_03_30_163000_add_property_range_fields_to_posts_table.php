<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->decimal('area_from', 10, 2)->nullable()->after('area');
            $table->decimal('area_to', 10, 2)->nullable()->after('area_from');
            $table->unsignedInteger('floor_count_from')->nullable()->after('floor_count');
            $table->unsignedInteger('floor_count_to')->nullable()->after('floor_count_from');
            $table->unsignedInteger('unit_count_from')->nullable()->after('unit_count');
            $table->unsignedInteger('unit_count_to')->nullable()->after('unit_count_from');
            $table->unsignedInteger('bedroom_count_from')->nullable()->after('bedroom_count');
            $table->unsignedInteger('bedroom_count_to')->nullable()->after('bedroom_count_from');
            $table->unsignedInteger('bathroom_count_from')->nullable()->after('bathroom_count');
            $table->unsignedInteger('bathroom_count_to')->nullable()->after('bathroom_count_from');
        });

        DB::table('posts')
            ->whereNotNull('area')
            ->update([
                'area_from' => DB::raw('area'),
                'area_to' => DB::raw('area'),
            ]);

        DB::table('posts')
            ->whereNotNull('floor_count')
            ->update([
                'floor_count_from' => DB::raw('floor_count'),
                'floor_count_to' => DB::raw('floor_count'),
            ]);

        DB::table('posts')
            ->whereNotNull('unit_count')
            ->update([
                'unit_count_from' => DB::raw('unit_count'),
                'unit_count_to' => DB::raw('unit_count'),
            ]);

        DB::table('posts')
            ->whereNotNull('bedroom_count')
            ->update([
                'bedroom_count_from' => DB::raw('bedroom_count'),
                'bedroom_count_to' => DB::raw('bedroom_count'),
            ]);

        DB::table('posts')
            ->whereNotNull('bathroom_count')
            ->update([
                'bathroom_count_from' => DB::raw('bathroom_count'),
                'bathroom_count_to' => DB::raw('bathroom_count'),
            ]);
    }

    public function down()
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn([
                'area_from',
                'area_to',
                'floor_count_from',
                'floor_count_to',
                'unit_count_from',
                'unit_count_to',
                'bedroom_count_from',
                'bedroom_count_to',
                'bathroom_count_from',
                'bathroom_count_to',
            ]);
        });
    }
};
