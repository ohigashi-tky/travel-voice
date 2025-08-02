# Docker起動エラー対応方法

## フロントエンド（Nuxt）のesbuildエラー

### 発生時期
2025年7月21日、Docker Desktop 4.43.2アップデート後にフロントエンドコンテナが起動しなくなった。

### エラー内容
```
ERROR: Cannot start service: Host version "0.25.5" does not match binary version "0.25.8"
Pre-transform error: The service is no longer running: write EPIPE
```

### 原因
1. **esbuildのプラットフォーム固有バイナリ問題**
   - macOS（ARM64）で`npm install`したesbuildバイナリ
   - DockerコンテナはAlpine Linux（x86_64）で実行
   - アーキテクチャ不整合によるバイナリ競合

2. **Docker Desktop 4.43.2での厳格化**
   - 以前は警告レベルだった互換性問題が致命的エラーに変更
   - セキュリティ強化によるバイナリチェック厳格化

### 解決方法

#### 1. フロントエンド用の.dockerignoreを作成
```bash
# frontend/.dockerignore
node_modules
package-lock.json
.npm
.nuxt
.output
dist
```

#### 2. コンテナの完全リビルド
```bash
# 現在のコンテナとイメージを削除
docker compose down
docker image rm guide-app-frontend

# 完全リビルド（--no-cacheが重要）
docker compose build frontend --no-cache

# 全サービス起動
docker compose up -d
```

#### 3. docker-compose.ymlの設定確認
```yaml
frontend:
  volumes:
    - ./frontend:/app
    - /app/node_modules  # コンテナ内のnode_modulesを優先
```

### 解決のポイント
- **ホストのnode_modules除外**: .dockerignoreでホスト側のバイナリを除外
- **コンテナ内でnpm install**: Linux用esbuildバイナリを正しくインストール
- **マウント設定**: コンテナ内のnode_modulesを優先使用

### 確認方法
```bash
# エラーなしでフロントエンドが起動することを確認
docker compose logs frontend

# 接続テスト
curl -I http://localhost:3000
```

### 類似問題
同様のエラーが発生する可能性があるツール：
- SWC
- Sharp
- その他のネイティブバイナリ依存パッケージ

### 参考
- Docker Desktop 4.40以降でのARM64エミュレーション変更
- esbuild GitHub Issues: "Docker + ARM64 compatibility"
- Nuxt 3公式: Apple Silicon Docker considerations