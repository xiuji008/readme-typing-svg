# syntax=docker/dockerfile:1

FROM php:8.1-apache

# Apache 文档根目录指向 src/
ENV APACHE_DOCUMENT_ROOT=/var/www/html/src

# 安装 Composer 解压依赖所需的系统工具与 zip 扩展
RUN apt-get update \
    && apt-get install -y --no-install-recommends git unzip libzip-dev \
    && docker-php-ext-install zip \
    && rm -rf /var/lib/apt/lists/*

# 启用 URL 重写并调整文档根目录配置
RUN a2enmod rewrite \
    && sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# 安装 Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# 先复制依赖清单以利用构建缓存
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist --no-interaction

# 复制项目源码并生成自动加载文件
COPY . .
RUN composer dump-autoload --optimize --no-dev

# Heroku 风格通过 $PORT 提供端口，默认 80
ENV PORT=80
RUN sed -ri -e 's!Listen 80!Listen ${PORT}!g' /etc/apache2/ports.conf \
    && sed -ri -e 's!<VirtualHost \*:80>!<VirtualHost *:${PORT}>!g' /etc/apache2/sites-available/*.conf

EXPOSE 80

# 字体目录（fonts.json 配置 + 上传/内置字体文件）单独挂载为数据卷，
# 容器重建后字体与配置可持久化。例如：
#   docker run -v /host/path/fonts:/var/www/html/src/fonts readme-typing-svg
VOLUME ["/var/www/html/src/fonts"]

CMD ["apache2-foreground"]
