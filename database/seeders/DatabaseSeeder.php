<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        $this->call(LaratrustSeeder::class);

       $user=User::factory()->create([
            'email'=>'a@admin.com',
            'name'=>'admin',
            'phone'=>'1234567890',
            'password'=>Hash::make('admin123'), // password
        ]);
        $user->addRole('admin');
    }
}
