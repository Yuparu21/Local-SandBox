<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 管理者ユーザー
        $admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('admin'),
        ]);
        $admin->status = 'active';
        $admin->role = 'admin';
        $admin->save();
        
        // 一般ユーザー
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('test'),
        ]);
        $user->status = 'active';
        $user->role = 'user';
        $user->save();
        
        // 停止中ユーザー
        $suspended = User::factory()->create([
            'name' => 'Suspended User',
            'email' => 'suspended@example.com',
            'password' => Hash::make('suspended'),
        ]);
        $suspended->status = 'suspended';
        $suspended->role = 'user';
        $suspended->save();
    }
}
