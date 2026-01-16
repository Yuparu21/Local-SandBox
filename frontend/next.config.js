/** @type {import('next').NextConfig} */
const nextConfig = {
  reactStrictMode: true,
  // Docker環境用の設定
  output: 'standalone',
  // 開発時のホットリロード設定
  webpack: (config, { dev, isServer }) => {
    if (dev) {
      config.watchOptions = {
        poll: 1000,
        aggregateTimeout: 300,
      }
    }
    return config
  },
}

module.exports = nextConfig
