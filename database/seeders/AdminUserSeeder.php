<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::where('email' ,'admin@project.com')->first();
        if (empty($user)) {
            User::create([
                'name' => 'admin',
                'email' => 'admin@project.com',
                'password' => bcrypt('Admin@1234'),
            ]);
        }
    }
}
