<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('apartments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('posts')->cascadeOnDelete();
            $table->string('name');
            $table->longText('content')->nullable();
            $table->decimal('price', 15, 2)->nullable();
            $table->decimal('area', 10, 2)->nullable();
            $table->unsignedInteger('bedroom_count')->nullable();
            $table->unsignedInteger('bathroom_count')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('apartments');
    }
};
