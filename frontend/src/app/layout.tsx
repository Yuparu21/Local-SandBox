import type { Metadata } from 'next'
import './globals.css'

export const metadata: Metadata = {
  title: 'Local SandBox',
  description: 'Next.js + Laravel + PostgreSQL Application',
}

export default function RootLayout({
  children,
}: {
  children: React.ReactNode
}) {
  return (
    <html lang="ja">
      <body>{children}</body>
    </html>
  )
}
