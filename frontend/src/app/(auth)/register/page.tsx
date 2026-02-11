'use client'

import { useState } from 'react'
import { useRegister } from '@/hooks/useRegister'
import { PasswordInput } from '@/components/PasswordInput'

export default function Register() {
  const [name, setName] = useState('')
  const [kana, setKana] = useState('')
  const [email, setEmail] = useState('')
  const [password, setPassword] = useState('')
  const [passwordConfirmation, setPasswordConfirmation] = useState('')
  const { register, errors, loading, success } = useRegister()

  const handleSubmit = async (e: React.FormEvent<HTMLFormElement>) => {
    e.preventDefault()
    await register({
      name,
      kana,
      email,
      password,
      password_confirmation: passwordConfirmation
    })
  }

  return (
    <>
      <div className="max-w-2xl w-full p-8 bg-white rounded-lg shadow-lg">
        <h1 className="text-2xl font-bold mb-8 text-center">会員登録</h1>

        {success && (
          <div className="mb-6 p-4 bg-green-100 text-green-700 rounded-md text-sm">
            登録が完了しました。確認メールを送信しましたので、メールアドレスを確認してください。
          </div>
        )}

        <form onSubmit={handleSubmit}>
          <div className="mb-5">
            <input
              type="text"
              value={name}
              onChange={(e) => setName(e.target.value)}
              placeholder="氏名（例: 山田太郎）"
              className={`w-full px-4 py-3 text-base border rounded-md focus:outline-none focus:ring-2 focus:ring-gray-900 ${
                errors.name ? 'border-red-500' : 'border-gray-300'
              }`}
              required
            />
            {errors.name && (
              <p className="mt-2 text-sm text-red-600">{errors.name[0]}</p>
            )}
          </div>

          <div className="mb-5">
            <input
              type="text"
              value={kana}
              onChange={(e) => setKana(e.target.value)}
              placeholder="フリガナ（例: ヤマダタロウ）"
              className={`w-full px-4 py-3 text-base border rounded-md focus:outline-none focus:ring-2 focus:ring-gray-900 ${
                errors.kana ? 'border-red-500' : 'border-gray-300'
              }`}
              required
            />
            {errors.kana && (
              <p className="mt-2 text-sm text-red-600">{errors.kana[0]}</p>
            )}
          </div>

          <div className="mb-5">
            <input
              type="email"
              value={email}
              onChange={(e) => setEmail(e.target.value)}
              placeholder="メールアドレス"
              className={`w-full px-4 py-3 text-base border rounded-md focus:outline-none focus:ring-2 focus:ring-gray-900 ${
                errors.email ? 'border-red-500' : 'border-gray-300'
              }`}
              required
            />
            {errors.email && (
              <p className="mt-2 text-sm text-red-600">{errors.email[0]}</p>
            )}
          </div>

          <div className="mb-5">
            <PasswordInput
              value={password}
              onChange={setPassword}
              placeholder="パスワード（8文字以上、大小英数記号）"
              error={errors.password?.[0]}
              required
            />
          </div>

          <div className="mb-6">
            <PasswordInput
              value={passwordConfirmation}
              onChange={setPasswordConfirmation}
              placeholder="パスワード（確認用）"
              error={errors.password_confirmation?.[0]}
              required
            />
          </div>

          <button
            type="submit"
            disabled={loading || success}
            className="w-full py-3 px-6 text-base font-semibold bg-gray-900 text-white rounded-md hover:bg-gray-700 disabled:bg-gray-400 transition-colors"
          >
            {loading ? '登録中...' : '登録する'}
          </button>
        </form>
      </div>
    </>
  )
}
