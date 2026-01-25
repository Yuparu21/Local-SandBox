<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

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
            'password' => 'admin',
        ]);
        $admin->role = 'admin';
        $admin->status = 'active';
        $admin->save();
        
        // 一般ユーザー
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'test',
        ]);
        $user->role = 'user';
        $user->status = 'active';
        $user->save();
        
        // 停止中ユーザー
        $suspended = User::factory()->create([
            'name' => 'Suspended User',
            'email' => 'suspended@example.com',
            'password' => 'suspended',
        ]);
        $suspended->role = 'user';
        $suspended->status = 'suspended';
        $suspended->save();
        
        
    }
}
