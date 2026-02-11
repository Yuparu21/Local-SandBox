'use client'

import {
  createContext,
  useState,
  useContext,
  useEffect,
  useCallback,
  ReactNode
} from 'react'
import type { User } from '@/types'

interface AuthContextType {
  user: User | null
  loading: boolean
  logout: () => Promise<void>
  refetch: () => Promise<void>
}

const AuthContext = createContext<AuthContextType | undefined>(undefined)

export function AuthProvider({ children }: { children: ReactNode }) {
  const [user, setUser] = useState<User | null>(null)
  const [loading, setLoading] = useState(true)
  const apiUrl = process.env.NEXT_PUBLIC_API_URL || 'http://localhost'

  const fetchUser = async () => {
    try {
      const res = await fetch(`${apiUrl}/api/user`, {
        headers: {
          'Accept': 'application/json',
        },
        credentials: 'include'
      })

      if (res.ok) {
        const data = await res.json()
        setUser(data)
      } else {
        setUser(null)
      }
    } catch (error) {
      setUser(null)
    } finally {
      setLoading(false)
    }
  }

  const logout = async () => {
    try {
      // CSRFトークンをクッキーから取得
      const getCsrfToken = () => {
        const cookies = document.cookie.split(';')
        const xsrfCookie = cookies.find(c => c.trim().startsWith('XSRF-TOKEN='))
        if (xsrfCookie) {
          return decodeURIComponent(xsrfCookie.split('=')[1])
        }
        return null
      }

      const csrfToken = getCsrfToken()
      const headers: HeadersInit = {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
      }

      if (csrfToken) {
        headers['X-XSRF-TOKEN'] = csrfToken
      }

      const res = await fetch(`${apiUrl}/api/logout`, {
        method: 'POST',
        headers,
        credentials: 'include'
      })
      setUser(null)
    } catch (error) {
      console.error('Logout failed:', error)
    }
  }

  useEffect(() => {
    fetchUser()
  }, [])

  return (
    <AuthContext.Provider value={{ user, loading, logout, refetch: fetchUser }}>
      {children}
    </AuthContext.Provider>
  )
}

export function useAuthContext() {
  const context = useContext(AuthContext)
  if (!context) {
    throw new Error('useAuthContext must be used within an AuthProvider')
  }
  return context
}
