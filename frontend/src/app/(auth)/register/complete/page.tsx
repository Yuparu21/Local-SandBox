'use client'

import Link from 'next/link'

export default function RegisterComplete() {
  return (
    <div className="max-w-2xl w-full mx-4 sm:mx-auto p-6 sm:p-8 bg-white rounded-lg shadow-lg">
      {/* Success Icon */}
      <div className="flex justify-center mb-4 sm:mb-6">
        <div className="w-14 h-14 sm:w-16 sm:h-16 bg-green-100 rounded-full flex items-center justify-center">
          <svg
            className="w-8 h-8 sm:w-10 sm:h-10 text-green-600"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path
              strokeLinecap="round"
              strokeLinejoin="round"
              strokeWidth={2}
              d="M5 13l4 4L19 7"
            />
          </svg>
        </div>
      </div>

      {/* Title */}
      <h1 className="text-xl sm:text-2xl font-bold mb-3 sm:mb-4 text-center text-gray-800">
        会員登録完了
      </h1>

      {/* Success Message */}
      <div className="mb-6 sm:mb-8 p-4 sm:p-6 bg-green-50 border border-green-200 rounded-lg">
        <div className="flex items-start gap-2 sm:gap-3 mb-2 sm:mb-3">
          <svg
            className="w-5 h-5 text-green-600 mt-0.5 flex-shrink-0"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path
              strokeLinecap="round"
              strokeLinejoin="round"
              strokeWidth={2}
              d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
            />
          </svg>
          <div className="text-green-800">
            <p className="font-semibold mb-1 text-sm sm:text-base">確認メールを送信しました</p>
            <p className="text-xs sm:text-sm text-green-700">
              ご登録いただいたメールアドレスに確認メールをお送りしました。
            </p>
          </div>
        </div>
        <div className="pl-7 sm:pl-8 text-xs sm:text-sm text-green-700">
          <p>メール内のリンクをクリックして、メールアドレスの確認を完了してください。</p>
        </div>
      </div>

      {/* Notice */}
      <div className="mb-4 sm:mb-6 p-3 sm:p-4 bg-gray-50 border border-gray-200 rounded-lg">
        <p className="text-xs text-gray-600 leading-relaxed">
          ※ メールが届かない場合は、迷惑メールフォルダをご確認ください。
          <br />※ 確認メールの有効期限は24時間です。
        </p>
      </div>

      {/* Back to Home Link */}
      <div className="text-center">
        <Link
          href="/"
          className="inline-flex items-center gap-1.5 sm:gap-2 text-sm sm:text-base text-blue-600 hover:text-blue-700 font-medium transition-colors"
        >
          <svg
            className="w-4 h-4 sm:w-5 sm:h-5"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path
              strokeLinecap="round"
              strokeLinejoin="round"
              strokeWidth={2}
              d="M10 19l-7-7m0 0l7-7m-7 7h18"
            />
          </svg>
          トップページに戻る
        </Link>
      </div>
    </div>
  )
}
