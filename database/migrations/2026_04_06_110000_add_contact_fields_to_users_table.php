<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddContactFieldsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('job_title')->nullable()->after('name');
            $table->text('bio')->nullable()->after('job_title');
            $table->string('address')->nullable()->after('bio');
            $table->string('phone')->nullable()->after('address');
            $table->string('secondary_phone')->nullable()->after('phone');
            $table->string('whatsapp_phone')->nullable()->after('secondary_phone');
            $table->string('avatar')->nullable()->after('whatsapp_phone');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'job_title',
                'bio',
                'address',
                'phone',
                'secondary_phone',
                'whatsapp_phone',
                'avatar',
            ]);
        });
    }
}
