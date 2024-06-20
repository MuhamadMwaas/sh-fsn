<?php

namespace Database\Seeders;
use Illuminate\Support\Facades\Hash;

use App\Models\Permission;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminRole = Permission::where('name', 'admin')->first();
        User::create([
            'name' => 'Admin',
            'email' => 'abdo@gmail.com',
            'password' => Hash::make('Abd111020033'),
            'rool_id' => $Admin->id,
        ]);
    }
}
