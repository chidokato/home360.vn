<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        User::updateOrCreate(
            ['email' => 'tuan.pn92@gmail.com'],
            [
                'name' => 'Jorge R.',
                'job_title' => 'Senior Property Manager',
                'bio' => 'Jorge R. is an experienced agent known for friendly service, local expertise, reliable property guidance across and nearby areas.',
                'address' => '6205 Peachtree Dunwoody Rd, Atlanta, GA 30328',
                'phone' => '1-555-678-8888',
                'secondary_phone' => '1-555-678-8888',
                'whatsapp_phone' => '15556788888',
                'avatar' => 'images/section/agent-2.1.jpg',
                'password' => Hash::make('123456'),
            ]
        );
    }
}
