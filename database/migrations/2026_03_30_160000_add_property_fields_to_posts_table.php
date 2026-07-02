<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->string('address', 1000)->nullable()->after('content');
            $table->decimal('area', 10, 2)->nullable()->after('address');
            $table->unsignedInteger('floor_count')->nullable()->after('area');
            $table->unsignedInteger('unit_count')->nullable()->after('floor_count');
            $table->unsignedInteger('bedroom_count')->nullable()->after('unit_count');
            $table->unsignedInteger('bathroom_count')->nullable()->after('bedroom_count');
        });
    }

    public function down()
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn([
                'address',
                'area',
                'floor_count',
                'unit_count',
                'bedroom_count',
                'bathroom_count',
            ]);
        });
    }
};
