FROM php:8.2-fpm

# Dependências do sistema + nginx + supervisor
RUN apt-get update && apt-get install -y \
    libpq-dev zip unzip git curl nginx supervisor \
    && docker-php-ext-install pdo pdo_pgsql opcache \
    && rm -rf /var/lib/apt/lists/*

# Configuração do OPcache para produção
RUN echo "opcache.enable=1\n\
opcache.memory_consumption=128\n\
opcache.interned_strings_buffer=8\n\
opcache.max_accelerated_files=10000\n\
opcache.revalidate_freq=0\n\
opcache.validate_timestamps=0" > /usr/local/etc/php/conf.d/opcache.ini

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Instala dependências de produção em camada separada (cache do Docker)
COPY composer.json composer.lock ./
RUN composer install --no-interaction --no-dev --optimize-autoloader --no-scripts

COPY . .
RUN composer dump-autoload --optimize

# Copia configurações do nginx e supervisor
COPY docker/nginx.conf /etc/nginx/sites-available/heroforce
RUN ln -sf /etc/nginx/sites-available/heroforce /etc/nginx/sites-enabled/default \
    && rm -f /etc/nginx/sites-enabled/default 2>/dev/null || true \
    && ln -sf /etc/nginx/sites-available/heroforce /etc/nginx/sites-enabled/heroforce

COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

RUN mkdir -p storage/framework/sessions storage/framework/views storage/framework/cache storage/logs \
    && chmod +x start.sh \
    && chown -R www-data:www-data storage bootstrap/cache

EXPOSE ${PORT:-8000}

CMD ["sh", "start.sh"]
