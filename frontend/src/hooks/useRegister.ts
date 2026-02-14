import { useState } from 'react'
import { useRouter } from 'next/navigation'

interface RegisterData {
  name: string
  kana: string
  email: string
  password: string
  password_confirmation: string
}

interface ValidationErrors {
  name?: string[]
  kana?: string[]
  email?: string[]
  password?: string[]
  password_confirmation?: string[]
}

export function useRegister() {
  const [errors, setErrors] = useState<ValidationErrors>({})
  const [loading, setLoading] = useState(false)
  const [success, setSuccess] = useState(false)
  const router = useRouter()
  const apiUrl = process.env.NEXT_PUBLIC_API_URL || 'http://localhost'

  const register = async (data: RegisterData) => {
    setLoading(true)
    setErrors({})
    setSuccess(false)

    try {
      // CSRF トークンを取得
      await fetch(`${apiUrl}/sanctum/csrf-cookie`, {
        credentials: 'include',
      })

      // 登録リクエスト
      const res = await fetch(`${apiUrl}/api/register`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          Accept: 'application/json',
        },
        body: JSON.stringify(data),
        credentials: 'include',
      })

      const responseData = await res.json()

      if (res.ok) {
        setSuccess(true)
        // 登録成功後、会員登録完了ページへリダイレクト
        router.push('/register/complete')
      } else if (res.status === 422) {
        // バリデーションエラー
        setErrors(responseData.errors || {})
      } else {
        // その他のエラー
        setErrors({
          email: [responseData.message || '登録に失敗しました。']
        })
      }
    } catch (error) {
      setErrors({
        email: ['通信エラーが発生しました。']
      })
    } finally {
      setLoading(false)
    }
  }

  return { register, errors, loading, success }
}
