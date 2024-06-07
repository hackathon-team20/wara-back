# ベースイメージとしてPHPの公式イメージを使用
FROM php:8.3.7

# 必要なPHP拡張機能をインストール
RUN apt-get update && apt-get install -y \
    libzip-dev \
    unzip \
    libpq-dev \
    && docker-php-ext-install zip pdo pdo_pgsql

# 作業ディレクトリの設定
WORKDIR /var/www

# Composerをインストール
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Laravelプロジェクトの依存関係をインストール
COPY composer.json composer.lock ./
RUN composer install --no-scripts --no-autoloader

# アプリケーションのソースコードをコピー
COPY . .

# 依存関係を再度インストール
RUN composer dump-autoload

# ポートの公開
EXPOSE 8000

# PHPビルトインサーバを起動
# CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
