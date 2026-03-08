# Coachtech 勤怠管理アプリ

## アプリケーション概要
勤怠の打刻と申請を行う、Coachtech課題用の勤怠管理アプリケーションです。  
一般ユーザーは出勤・休憩・退勤の打刻、勤怠詳細の確認、修正申請ができます。  
管理者はスタッフ別勤怠の閲覧、修正申請の承認、CSVエクスポートができます。

## 主な機能
- ユーザー認証（一般ユーザー / 管理者）
- 出勤・休憩開始・休憩終了・退勤打刻
- 月次勤怠一覧の表示
- 勤怠修正申請と申請一覧表示
- 管理者による修正申請の承認
- スタッフ別勤怠CSVエクスポート

## 使用技術（実行環境）
- `PHP`: 8.1（テスト実行は `php8.1` を使用）
- `Framework`: Laravel 8.75
- `Database`: MySQL 8.0.26
- `Web Server`: Nginx 1.21.1
- `Infrastructure`: Docker / Docker Compose
- `Mail`: MailHog

## 環境構築手順

### 1. リポジトリを取得
```bash
git clone git@github.com:takedayuuki1111/kintai.git
cd kintai
```

### 2. 環境変数ファイルを作成
```bash
cd src
cp .env.example .env
cd ..
```

`.env` のDB接続が以下になっていることを確認してください。

```env
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel_db
DB_USERNAME=laravel_user
DB_PASSWORD=laravel_pass
```

### 3. Dockerコンテナを起動
```bash
docker-compose up -d --build
```

### 4. 依存関係インストール・アプリ初期化
```bash
docker-compose exec php composer install
docker-compose exec php php artisan key:generate
docker-compose exec php php artisan migrate --seed
```

### 5. 必要に応じて権限を調整
```bash
docker-compose exec php chmod -R 775 storage bootstrap/cache
```

## 画面URL
- アプリ: `http://localhost/login`
- 管理者ログイン: `http://localhost/admin/login`
- phpMyAdmin: `http://localhost:8080`
- MailHog: `http://localhost:8025`

## テストアカウント（Seeder初期データ）
| 種別 | メールアドレス | パスワード |
| 管理者 | `admin@example.com` | `password` |
| 一般ユーザー | `test@example.com` | `password` |

## テスト実行
```bash
cd src
/usr/bin/php8.1 ./vendor/bin/phpunit
```

## 補足
- ローカルに `docker/mysql/data` 配下が未追跡で増えることがあります（MySQLの実行データ）。
- 権限エラーが出る場合は、`src/storage` および `src/bootstrap/cache` の書き込み権限を確認してください。
- `test@example.com` でログインできない場合は、Seederを再実行してください。

```bash
docker-compose exec php php artisan db:seed --class=Database\\Seeders\\DatabaseSeeder --force
```

## 変更仕様書（再提出）
- テーブル仕様・Seeder変更内容: `docs/table-spec-and-seeder-notes.md`


