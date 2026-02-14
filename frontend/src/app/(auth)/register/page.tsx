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
  const { register, errors, loading } = useRegister()

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
    <div className="max-w-2xl w-full mx-4 sm:mx-auto p-6 sm:p-8 bg-white rounded-lg shadow-lg">
      <h1 className="text-xl sm:text-2xl font-bold mb-6 sm:mb-8 text-center">会員登録</h1>
      <form onSubmit={handleSubmit}>
        <div className="mb-4 sm:mb-5">
          <input
            type="text"
            value={name}
            onChange={(e) => setName(e.target.value)}
            placeholder="氏名（例: 山田太郎）"
            className={`w-full px-3 sm:px-4 py-2.5 sm:py-3 text-sm sm:text-base border rounded-md focus:outline-none focus:ring-2 focus:ring-gray-900 ${errors.name ? 'border-red-500' : 'border-gray-300'
              }`}
            required
          />
          {errors.name && (
            <p className="mt-1.5 sm:mt-2 text-xs sm:text-sm text-red-600">{errors.name[0]}</p>
          )}
        </div>

        <div className="mb-4 sm:mb-5">
          <input
            type="text"
            value={kana}
            onChange={(e) => setKana(e.target.value)}
            placeholder="フリガナ（例: ヤマダタロウ）"
            className={`w-full px-3 sm:px-4 py-2.5 sm:py-3 text-sm sm:text-base border rounded-md focus:outline-none focus:ring-2 focus:ring-gray-900 ${errors.kana ? 'border-red-500' : 'border-gray-300'
              }`}
            required
          />
          {errors.kana && (
            <p className="mt-1.5 sm:mt-2 text-xs sm:text-sm text-red-600">{errors.kana[0]}</p>
          )}
        </div>

        <div className="mb-4 sm:mb-5">
          <input
            type="email"
            value={email}
            onChange={(e) => setEmail(e.target.value)}
            placeholder="メールアドレス"
            className={`w-full px-3 sm:px-4 py-2.5 sm:py-3 text-sm sm:text-base border rounded-md focus:outline-none focus:ring-2 focus:ring-gray-900 ${errors.email ? 'border-red-500' : 'border-gray-300'
              }`}
            required
          />
          {errors.email && (
            <p className="mt-1.5 sm:mt-2 text-xs sm:text-sm text-red-600">{errors.email[0]}</p>
          )}
        </div>

        <div className="mb-4 sm:mb-5">
          <PasswordInput
            value={password}
            onChange={setPassword}
            placeholder="パスワード（8文字以上、大小英数記号）"
            error={errors.password?.[0]}
            required
          />
        </div>

        <div className="mb-5 sm:mb-6">
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
          disabled={loading}
          className="w-full py-2.5 sm:py-3 px-4 sm:px-6 text-sm sm:text-base font-semibold bg-gray-900 text-white rounded-md hover:bg-gray-700 disabled:bg-gray-400 transition-colors"
        >
          {loading ? '登録中...' : '登録する'}
        </button>
      </form>
    </div>
  )
}
