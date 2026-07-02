<?php

use App\Models\PostImage;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('post_images', function (Blueprint $table) {
            $table->string('image_type', 30)
                ->default(PostImage::TYPE_PERSPECTIVE)
                ->after('image');
        });

        DB::table('post_images')
            ->whereNull('image_type')
            ->update([
                'image_type' => PostImage::TYPE_PERSPECTIVE,
            ]);
    }

    public function down()
    {
        Schema::table('post_images', function (Blueprint $table) {
            $table->dropColumn('image_type');
        });
    }
};
