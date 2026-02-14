'use client'

import { useEffect, useState } from 'react'
import { useSearchParams } from 'next/navigation'
import { useVerifyEmail } from '@/hooks/useVerifyEmail'

export default function VerifyEmail() {
  const searchParams = useSearchParams()
  const token = searchParams.get('token')
  const { verifyEmail, error, loading, success } = useVerifyEmail()
  const [attempted, setAttempted] = useState(false)

  useEffect(() => {
    if (token && !attempted) {
      setAttempted(true)
      verifyEmail(token)
    }
  }, [token, attempted, verifyEmail])

  if (!token) {
    return (
      <div className="max-w-2xl w-full mx-4 sm:mx-auto p-6 sm:p-8 bg-white rounded-lg shadow-lg">
        <h1 className="text-xl sm:text-2xl font-bold mb-6 sm:mb-8 text-center">メールアドレス確認</h1>
        <div className="p-3 sm:p-4 bg-red-100 text-red-700 rounded-md text-xs sm:text-sm">
          無効な確認リンクです。
        </div>
      </div>
    )
  }

  return (
    <div className="max-w-2xl w-full mx-4 sm:mx-auto p-6 sm:p-8 bg-white rounded-lg shadow-lg">
      <h1 className="text-xl sm:text-2xl font-bold mb-6 sm:mb-8 text-center">メールアドレス確認</h1>

      {loading && (
        <div className="text-center">
          <div className="inline-block animate-spin rounded-full h-10 w-10 sm:h-12 sm:w-12 border-b-2 border-gray-900 mb-3 sm:mb-4"></div>
          <p className="text-gray-600 text-sm sm:text-base">確認中...</p>
        </div>
      )}

      {success && (
        <div className="p-3 sm:p-4 bg-green-100 text-green-700 rounded-md">
          <p className="font-semibold mb-1.5 sm:mb-2 text-sm sm:text-base">メールアドレスの確認が完了しました！</p>
          <p className="text-xs sm:text-sm">TOPページに移動します...</p>
        </div>
      )}

      {error && (
        <div className="p-3 sm:p-4 bg-red-100 text-red-700 rounded-md">
          <p className="font-semibold mb-1.5 sm:mb-2 text-sm sm:text-base">確認に失敗しました</p>
          <p className="text-xs sm:text-sm">{error}</p>
        </div>
      )}
    </div>
  )
}
