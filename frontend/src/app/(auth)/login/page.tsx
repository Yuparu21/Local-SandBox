'use client'

import { useState } from 'react'
import { useLogin } from '@/hooks/useLogin'
import { PasswordInput } from '@/components/PasswordInput'

export default function Login() {
  const [email, setEmail] = useState('')
  const [password, setPassword] = useState('')
  const { login, error, loading } = useLogin()

  const handleSubmit = async (e: React.FormEvent<HTMLFormElement>) => {
    e.preventDefault()
    await login({ email, password })
  }

  return (
    <div className="max-w-2xl w-full mx-4 sm:mx-auto p-6 sm:p-8 bg-white rounded-lg shadow-lg">
      <h1 className="text-xl sm:text-2xl font-bold mb-6 sm:mb-8 text-center">ログイン</h1>

      {error && (
        <div className="mb-4 sm:mb-6 p-3 sm:p-4 bg-red-100 text-red-700 rounded-md text-xs sm:text-sm">
          {error}
        </div>
      )}

      <form onSubmit={handleSubmit}>
        <div className="mb-4 sm:mb-5">
          <input
            type="email"
            value={email}
            onChange={(e) => setEmail(e.target.value)}
            placeholder="メールアドレス"
            className="w-full px-3 sm:px-4 py-2.5 sm:py-3 text-sm sm:text-base border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-900"
            required
          />
        </div>
        <div className="mb-5 sm:mb-6">
          <PasswordInput
            value={password}
            onChange={setPassword}
            required
          />
        </div>
        <button
          type="submit"
          disabled={loading}
          className="w-full py-2.5 sm:py-3 px-4 sm:px-6 text-sm sm:text-base font-semibold bg-gray-900 text-white rounded-md hover:bg-gray-700 disabled:bg-gray-400 transition-colors"
        >
          {loading ? 'ログイン中...' : 'ログイン'}
        </button>
      </form>
    </div>
  )
}
