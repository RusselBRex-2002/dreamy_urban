<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('users')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $exists = User::where('email', 'adminn@gmail.com')->first();

        if (!$exists) {
            $user = User::create([
                'name' => 'Admin',
                'email' => 'admin@gmail.com',
                'email_verified_at' => date('Y-m-d H:i:s'),
                'password' => Hash::make('Admin@123'),
            ]);
            $user->assignRole('Admin');
        }
    }
}
