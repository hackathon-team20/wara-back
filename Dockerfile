# ベースイメージとしてPHPの公式イメージを使用
FROM php:8.3.7-apache

# 必要なPHP拡張機能をインストール
RUN apt-get update && apt-get install -y \
    libzip-dev \
    unzip \
    libpq-dev \
    && docker-php-ext-install zip pdo pdo_pgsql

# ApacheのドキュメントルートをLaravelのpublicディレクトリに変更
ENV APACHE_DOCUMENT_ROOT /var/www/public

# Apacheの設定ファイルを変更してドキュメントルートを設定
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

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

# Apacheモジュールを有効にする
RUN a2enmod rewrite

# ポートの公開
EXPOSE 80

# Apacheをフォアグラウンドで起動
# CMD ["apache2-foreground"]