export default function Home() {
  return (
    <main className="min-h-screen flex items-center justify-center bg-gray-100">
      <div className="text-center">
        <h1 className="text-4xl font-bold text-gray-800 mb-4">
          Welcome to Local SandBox
        </h1>
        <p className="text-lg text-gray-600 mb-8">
          Next.js + Laravel + PostgreSQL + Nginx
        </p>
        <div className="space-y-4">
          <div className="bg-white rounded-lg shadow-md p-6">
            <h2 className="text-xl font-semibold text-blue-600">Frontend</h2>
            <p className="text-gray-500">Next.js (React)</p>
          </div>
          <div className="bg-white rounded-lg shadow-md p-6">
            <h2 className="text-xl font-semibold text-red-600">Backend</h2>
            <p className="text-gray-500">Laravel (PHP)</p>
          </div>
          <div className="bg-white rounded-lg shadow-md p-6">
            <h2 className="text-xl font-semibold text-green-600">Database</h2>
            <p className="text-gray-500">PostgreSQL</p>
          </div>
        </div>
      </div>
    </main>
  )
}
