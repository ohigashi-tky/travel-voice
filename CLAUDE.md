# Claude Code プロジェクト設定

## 言語設定
- **必須**: 全ての回答は日本語で行ってください
- コメントやドキュメントも日本語を優先してください

## プロジェクト概要
- Laravel + Nuxt.js による観光ガイドアプリケーション
- Amazon Polly による音声ガイド機能を実装
- 主要技術スタック：
  - バックエンド: Laravel 11, PHP 8.2
  - フロントエンド: Nuxt.js 3, Vue 3, Tailwind CSS
  - 音声合成: Amazon Polly (Takumi/Tomoko)

## 開発時の注意事項
- 音声ファイルは public storage に保存
- Laravel の storage:link が必要
- 24時間キャッシュによる音声ガイドテキスト管理
- 全てのUIコンポーネントでIcon依存を避け、絵文字使用を推奨

## テストコマンド
- フロントエンド: `npm run dev` (ポート: 3000)
- バックエンド: `php artisan serve --port=8000`
- ストレージリンク: `php artisan storage:link`