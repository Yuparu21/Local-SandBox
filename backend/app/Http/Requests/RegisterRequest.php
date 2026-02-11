<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:50',
            ],
            'kana' => [
                'required',
                'string',
                'max:50',
                'regex:/^[ァ-ヴー\s　]+$/u',
            ],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:users,email',
            ],
            'password' => [
                'required',
                'string',
                'min:8',
                'max:72',
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&#])[A-Za-z\d@$!%*?&#]+$/',
            ],
        ];
    }

    /**
     * Get custom validation messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => '氏名は必須です。',
            'name.max' => '氏名は50文字以内で入力してください。',
            'kana.required' => 'フリガナは必須です。',
            'kana.max' => 'フリガナは50文字以内で入力してください。',
            'kana.regex' => 'フリガナは全角カタカナで入力してください。',
            'email.required' => 'メールアドレスは必須です。',
            'email.unique' => 'このメールアドレスは既に登録されています。',
            'email.max' => 'メールアドレスは255文字以内で入力してください。',
            'passsword.required' => 'パスワードは必須です。',
            'password.min' => 'パスワードは8文字以上で入力してください。',
            'password.max' => 'パスワードは72文字以内で入力してください。',
            'password.confirmed' => 'パスワード確認が一致しません。',
            'password.regex' => 'パスワードは英大文字、小文字、数字、記号(@$!%*?&#)をそれぞれ1文字以上含む必要があります。',
        ];
    }
}
