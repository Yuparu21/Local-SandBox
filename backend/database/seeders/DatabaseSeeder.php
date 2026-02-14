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
            'name' => '管理者',
            'kana' => 'カンリシャ',
            'email' => 'admin@example.com',
            'password' => Hash::make('Admin1234!@#'),
        ]);
        $admin->status = 'active';
        $admin->role = 'admin';
        $admin->save();
        
        // 一般ユーザー
        $user = User::factory()->create([
            'name' => 'テストユーザー',
            'kana' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => Hash::make('Test1234!@#'),
        ]);
        $user->status = 'active';
        $user->role = 'user';
        $user->save();
        
        // 停止中ユーザー
        $suspended = User::factory()->create([
            'name' => '停止ユーザー',
            'kana' => 'テイシユーザー',
            'email' => 'suspended@example.com',
            'password' => Hash::make('Suspended1234!@#'),
        ]);
        $suspended->status = 'suspended';
        $suspended->role = 'user';
        $suspended->save();
        
        // 追加のテストユーザー（ランダム生成）
        User::factory()->count(10)->create();
    }
}
