'use client'

import Link from 'next/link'
import { useRouter } from 'next/navigation'
import { useAuthContext } from '@/contexts/AuthContext'

export default function Header() {
  const { user, loading, logout } = useAuthContext()
  const router = useRouter()

  const handleLogout = async () => {
    await logout()
    router.push('/login')
  }

  return (
    <header className="bg-white border-b border-gray-200">
      <div className="max-w-4xl mx-auto px-4 py-4">
        <nav className="flex items-center justify-between">
          <Link href="/" className="text-xl font-bold text-gray-900 hover:text-gray-700">
            Local SandBox
          </Link>
          <ul className="flex gap-6 items-center">
            {loading ? (
              <li className="text-gray-400">読み込み中...</li>
            ) : user ? (
              <>
                <li className="text-gray-900">
                  {user.name}
                </li>
                <li>
                  <button
                    onClick={handleLogout}
                    className="text-gray-600 hover:text-gray-900"
                  >
                    ログアウト
                  </button>
                </li>
              </>
            ) : (
              <li>
                <Link href="/login" className="text-gray-600 hover:text-gray-900">
                  ログイン
                </Link>
              </li>
            )}
          </ul>
        </nav>
      </div>
    </header>
  )
}

