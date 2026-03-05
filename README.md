# Kintai (勤怠管理システム)

> ⚠️ **ストレージの権限によりテストが失敗する場合**
> ```bash
> chmod -R 775 src/storage
> ```
> としてログファイルに書き込みできるようにしてください。


このリポジトリは、Docker コンテナ上で動作する Laravel ベースの勤怠管理アプリケーションのサンプルです。

## 概要

- ユーザーは出勤・休憩・退勤の打刻、勤怠修正の申請を行えます。
- 管理者はスタッフ一覧の閲覧、CSV エクスポート、勤怠修正を行えます。

## 必要環境

- [Docker](https://www.docker.com/)
- [Docker Compose](https://docs.docker.com/compose/)

## セットアップ手順

1. プロジェクトをクローンします。
   ```bash
   git clone <repo-url> kintai
   cd kintai
   ```
2. コンテナをビルド＆起動します。
   ```bash
   docker-compose build
   docker-compose up -d
   ```
3. 依存関係をインストールし、マイグレーション・シーディングを実行します。
   ```bash
   # php コンテナ内で行う
   docker-compose exec php bash
   php artisan migrate --seed
   ```
4. ブラウザで `http://localhost` にアクセスします。

## ログイン情報（初期データ）

- 管理者
  - メール: `admin@example.com`
  - パスワード: `password`
- ユーザー
  - メール: `test@example.com`
  - パスワード: `password`

## 主要コマンド

```bash
# テスト実行
php artisan test

# キャッシュクリア
php artisan config:clear
php artisan route:clear
php artisan view:clear
```


