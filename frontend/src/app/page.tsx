export default async function Top() {
  const apiUrl = process.env.NEXT_PUBLIC_APP_URL || 'http://localhost:3000';
  const adminUser = await fetch(`${apiUrl}/api/user`, {
    cache: 'no-store' // 常に最新データを取得
  }).then(res => res.json());
  
  return (
    <>
      <h1>Top Page</h1>
      <div>
          <p>ようこそ、{adminUser.name}さん</p>
      </div>
    </>
  )
}
