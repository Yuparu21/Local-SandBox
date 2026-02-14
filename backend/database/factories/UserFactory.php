<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $lastName = fake()->lastName();
        $firstName = fake()->firstName();
        $name = $lastName . $firstName;
        
        // カタカナ変換用の簡易マッピング（実際の実装ではより詳細なマッピングが必要）
        $kanaMap = [
            'あ' => 'ア', 'い' => 'イ', 'う' => 'ウ', 'え' => 'エ', 'お' => 'オ',
            'か' => 'カ', 'き' => 'キ', 'く' => 'ク', 'け' => 'ケ', 'こ' => 'コ',
            'さ' => 'サ', 'し' => 'シ', 'す' => 'ス', 'せ' => 'セ', 'そ' => 'ソ',
            'た' => 'タ', 'ち' => 'チ', 'つ' => 'ツ', 'て' => 'テ', 'と' => 'ト',
            'な' => 'ナ', 'に' => 'ニ', 'ぬ' => 'ヌ', 'ね' => 'ネ', 'の' => 'ノ',
            'は' => 'ハ', 'ひ' => 'ヒ', 'ふ' => 'フ', 'へ' => 'ヘ', 'ほ' => 'ホ',
            'ま' => 'マ', 'み' => 'ミ', 'む' => 'ム', 'め' => 'メ', 'も' => 'モ',
            'や' => 'ヤ', 'ゆ' => 'ユ', 'よ' => 'ヨ',
            'ら' => 'ラ', 'り' => 'リ', 'る' => 'ル', 'れ' => 'レ', 'ろ' => 'ロ',
            'わ' => 'ワ', 'を' => 'ヲ', 'ん' => 'ン',
        ];
        
        // ランダムなカタカナを生成（簡易実装）
        $kanaLength = fake()->numberBetween(4, 10);
        $katakana = ['ア', 'イ', 'ウ', 'エ', 'オ', 'カ', 'キ', 'ク', 'ケ', 'コ', 
                     'サ', 'シ', 'ス', 'セ', 'ソ', 'タ', 'チ', 'ツ', 'テ', 'ト',
                     'ナ', 'ニ', 'ヌ', 'ネ', 'ノ', 'ハ', 'ヒ', 'フ', 'ヘ', 'ホ',
                     'マ', 'ミ', 'ム', 'メ', 'モ', 'ヤ', 'ユ', 'ヨ',
                     'ラ', 'リ', 'ル', 'レ', 'ロ', 'ワ', 'ン'];
        $kana = '';
        for ($i = 0; $i < $kanaLength; $i++) {
            $kana .= $katakana[array_rand($katakana)];
        }
        
        return [
            'name' => $name,
            'kana' => $kana,
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
