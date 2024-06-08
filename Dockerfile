# ベースイメージとしてPHPの公式イメージを使用
FROM php:8.3-apache

# 必要なPHP拡張モジュールをインストール
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    zip \
    unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd \
    && docker-php-ext-install pdo_mysql zip

# Composerのインストール
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Laravelアプリケーションのソースコードをコピー
COPY . /var/www/html

# Apache設定ファイルのコピー
COPY ./apache/000-default.conf /etc/apache2/sites-available/000-default.conf

# Apacheモジュールの有効化
RUN a2enmod rewrite

# 権限の設定
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Laravelの依存関係をインストール
WORKDIR /var/www/html


# Laravelの設定ファイルのキャッシュ化
RUN php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache

# Laravelアプリケーションキーの生成
RUN php artisan key:generate

# Apacheをフォアグラウンドで実行
CMD ["apache2-foreground"]
