# Local SandBox

ローカル開発用Docker環境

## 技術スタック

### 言語

| 言語 | バージョン |
|------|-----------|
| PHP | 8.3 |
| TypeScript | 5.3.0 |

### フレームワーク

| 用途 | フレームワーク | バージョン | ポート |
|------|---------------|-----------|--------|
| Frontend | Next.js | 15.1.3 | 3000 |
| Frontend | React | 19.0.0 | - |
| Backend | Laravel | 12.0 | 80 |

### インフラストラクチャ

| 種別 | 技術 | バージョン | ポート |
|------|------|-----------|--------|
| APPサーバ (Frontend) | Node.js | 20.x | - |
| APPサーバ (Backend) | PHP-FPM | 8.3 | 9000 |
| Webサーバ | Nginx | Alpine | 80 |
| データベース | PostgreSQL | 15 (Alpine) | 5432 |

## ディレクトリ構成

```
.
├── docker-compose.yml
├── docker/
│   ├── nginx/
│   │   └── default.conf
│   ├── php/
│   │   └── Dockerfile
│   └── frontend/
│       └── Dockerfile
├── backend/          # Laravel
└── frontend/         # Next.js
```

## セットアップ

### 初回セットアップ（1回のみ）

以下は最初の環境構築時のみ実行してください。

#### 1. 環境変数の設定

```bash
cp backend/.env.local backend/.env
```

#### 2. Dockerコンテナの起動（初回ビルド）

```bash
docker-compose up -d --build
```

#### 3. Laravel初期設定

```bash
# 依存関係インストール
docker-compose exec php composer install

# アプリケーションキー生成（暗号化に必要）
docker-compose exec php php artisan key:generate

# データベースのテーブル作成
docker-compose exec php php artisan migrate
```

#### 4. フロントエンド依存関係インストール

```bash
docker-compose exec frontend npm install
```

---

### 通常起動（2回目以降）

環境を立ち上げる度に実行してください。

```bash
# コンテナ起動
docker-compose up -d

# コンテナ停止
docker-compose down
```

> **Note**: コードを変更しただけであれば `--build` は不要です。Dockerfileや依存関係を変更した場合のみ `docker-compose up -d --build` を使用してください。

## アクセス

http://localhost:3000

## よく使うコマンド

```bash
# コンテナ起動
docker-compose up -d

# コンテナ停止
docker-compose down

# ログ確認
docker-compose logs -f

# 特定サービスのログ
docker-compose logs -f php

# PHPコンテナに入る
docker-compose exec php bash

# Node.jsコンテナに入る
docker-compose exec frontend sh

# PostgreSQLに接続
docker-compose exec postgres psql -U dev -d devdb
```

## データベース接続情報

| 項目 | 値 |
|------|-----|
| Host | postgres |
| Port | 5432 |
| Database | devdb |
| Username | dev |
| Password | dev |

