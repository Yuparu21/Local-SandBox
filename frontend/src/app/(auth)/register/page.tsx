'use client'

import { useState } from 'react'
import { useLogin } from '@/hooks/useLogin'
import { PasswordInput } from '@/components/PasswordInput'

export default function Register() {
  const [name, setName] = useState('')
  const [email, setEmail] = useState('')
  const [password, setPassword] = useState('')
  const { login, error, loading } = useLogin()

  const handleSubmit = async (e: React.FormEvent<HTMLFormElement>) => {
    e.preventDefault()
    // await login({ email, password })
    // 仮実装: 会員登録機能は未実装のため、ログイン関数を呼び出さない
  }

  return (
    <>
      <div className="max-w-lg w-full p-8 bg-white rounded-lg shadow-lg">
        <h1 className="text-2xl font-bold mb-8 text-center">会員登録</h1>

        {error && (
          <div className="mb-6 p-4 bg-red-100 text-red-700 rounded-md">
            {error}
          </div>
        )}

        <form onSubmit={handleSubmit}>
          <div className="mb-6">
            <input
              type="text"
              value={name}
              onChange={(e) => setName(e.target.value)}
              placeholder="氏名"
              className="w-full px-4 py-4 text-lg border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-900"
              required
            />
          </div>
          <div className="mb-6">
            <input
              type="email"
              value={email}
              onChange={(e) => setEmail(e.target.value)}
              placeholder="メールアドレス"
              className="w-full px-4 py-4 text-lg border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-900"
              required
            />
          </div>
          <div className="mb-8">
            <PasswordInput
              value={password}
              onChange={setPassword}
              required
            />
          </div>
          <button
            type="submit"
            disabled={loading}
            className="w-full py-4 px-6 text-lg font-semibold bg-gray-900 text-white rounded-md hover:bg-gray-700 disabled:bg-gray-400 transition-colors"
          >
            {loading ? '登録中...' : '登録する'}
          </button>
        </form>
      </div>
    </>
  )
}
