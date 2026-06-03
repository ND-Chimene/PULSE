# Stage 1 : compile JS/CSS assets (needed for entrypoints.json)
FROM node:22-alpine AS assets

WORKDIR /app

RUN npm install -g pnpm

COPY package.json pnpm-lock.yaml ./
RUN pnpm install --frozen-lockfile --ignore-scripts

COPY webpack.config.js postcss.config.js tailwind.config.js ./
COPY assets ./assets
COPY templates ./templates

RUN pnpm run build

# Stage 2 : PHP-FPM
FROM php:8.4-fpm-alpine

RUN apk --no-cache add curl git wget bash dpkg acl shadow ca-certificates openssl \
    && update-ca-certificates

ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/
RUN chmod +x /usr/local/bin/install-php-extensions \
    && install-php-extensions opcache iconv soap zip intl fileinfo pdo redis mysqli pdo_sqlite gd

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin/ --filename=composer

WORKDIR /var/www
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-scripts
COPY . .

# Inject compiled assets so Symfony knows the correct hashed filenames
COPY --from=assets /app/public/build /var/www/public/build

ENV APP_ENV=prod
ENV APP_DEBUG=0

RUN mkdir -p var/cache var/log \
    && chown -R www-data:www-data /var/www \
    && chmod -R 755 /var/www/vendor \
    && chmod -R 750 /var/www/var

COPY docker/php/init.sh /usr/local/bin/init.sh
RUN chmod +x /usr/local/bin/init.sh
CMD ["/usr/local/bin/init.sh"]
