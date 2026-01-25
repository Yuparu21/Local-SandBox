import Link from 'next/link'

export default function Header() {
  return (
    <header className="bg-white border-b border-gray-200">
      <div className="max-w-4xl mx-auto px-4 py-4">
        <nav className="flex items-center justify-between">
          <Link href="/" className="text-xl font-bold text-gray-900 hover:text-gray-700">
            Local SandBox
          </Link>
          <ul className="flex gap-6">
            <li>
              <Link href="/login" className="text-gray-600 hover:text-gray-900">
                ログイン
              </Link>
            </li>
          </ul>
        </nav>
      </div>
    </header>
  )
}
