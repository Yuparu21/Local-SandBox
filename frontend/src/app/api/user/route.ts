export async function GET() {
  try {
    const res = await fetch('http://nginx/api/user');
    
    if (!res.ok) {
      throw new Error(`Laravel API error: ${res.status}`);
    }
    
    const data = await res.json();
    return Response.json(data);
  } catch (error) {
    console.error('Failed to fetch user:', error);
    return Response.json(
      { error: 'Failed to fetch user data' },
      { status: 500 }
    );
  }
}
