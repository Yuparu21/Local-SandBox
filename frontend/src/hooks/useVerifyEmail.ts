import { useState } from 'react'
import { useRouter } from 'next/navigation'

export function useVerifyEmail() {
  const [error, setError] = useState('')
  const [loading, setLoading] = useState(false)
  const [success, setSuccess] = useState(false)
  const router = useRouter()
  const apiUrl = process.env.NEXT_PUBLIC_API_URL || 'http://localhost'

  const verifyEmail = async (token: string) => {
    setLoading(true)
    setError('')
    setSuccess(false)

    try {
      // CSRF トークンを取得
      await fetch(`${apiUrl}/sanctum/csrf-cookie`, {
        credentials: 'include',
      })

      // メール確認リクエスト
      const res = await fetch(`${apiUrl}/api/email/verify`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          Accept: 'application/json',
        },
        body: JSON.stringify({ token }),
        credentials: 'include',
      })

      const data = await res.json()

      if (res.ok) {
        setSuccess(true)
        // 確認成功後、Topページへリダイレクト
        setTimeout(() => {
          router.push('/')
        }, 2000)
      } else {
        setError(data.message || 'メールアドレスの確認に失敗しました。')
      }
    } catch (error) {
      setError('通信エラーが発生しました。')
    } finally {
      setLoading(false)
    }
  }

  return { verifyEmail, error, loading, success }
}
