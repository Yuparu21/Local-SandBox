import { useState } from "react";
import { useRouter } from "next/navigation";
import { useAuthContext } from "@/contexts/AuthContext";

interface LoginCredentials {
  email: string;
  password: string;
}

export function useLogin() {
  const [error, setError] = useState("");
  const [loading, setLoading] = useState(false);
  const router = useRouter();
  const { refetch } = useAuthContext();
  const apiUrl = process.env.NEXT_PUBLIC_API_URL || "http://localhost";

  const login = async ({ email, password }: LoginCredentials) => {
    setLoading(true);
    setError("");

    try {
      // CSRF トークンを取得
      await fetch(`${apiUrl}/sanctum/csrf-cookie`, {
        credentials: "include",
      });

      // ログインリクエスト
      const res = await fetch(`${apiUrl}/api/login`, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          Accept: "application/json",
        },
        body: JSON.stringify({ email, password }),
        credentials: "include",
      });

      if (res.ok) {
        // ユーザー情報を再取得
        await refetch();
        // SPAナビゲーション
        router.push("/");
      } else {
        const data = await res.json();
        
        // メール未確認エラーの場合
        if (res.status === 403 && data.message) {
          setError(data.message);
        }
        // バリデーションエラーの場合
        else if (data.errors && data.errors.email) {
          setError(data.errors.email[0]);
        }
        // 認証失敗の場合
        else if (res.status === 401) {
          setError(
            "入力されたメールアドレスまたはパスワードが正しくありません。",
          );
        }
        // その他のエラー
        else {
          setError(data.message || "ログインに失敗しました。");
        }
      }
    } catch (error) {
      setError("通信エラーが発生しました。");
    } finally {
      setLoading(false);
    }
  };

  return { login, error, loading };
}
