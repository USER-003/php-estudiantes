# App 1 (PHP) para Kompilo. Compatible con el chart: corre como usuario 10001,
# raiz de solo lectura, solo /tmp escribible y escucha en $PORT.
FROM php:8.3-cli-alpine

RUN apk add --no-cache libpq \
 && apk add --no-cache --virtual .build postgresql-dev \
 && docker-php-ext-install pdo_pgsql \
 && apk del .build

WORKDIR /app
COPY . .

ENV PORT=8080 PHP_CLI_SERVER_WORKERS=4
# Las sesiones y temporales de PHP van a /tmp (el unico directorio escribible)
CMD ["sh", "-c", "exec php -d session.save_path=/tmp -d upload_tmp_dir=/tmp -S 0.0.0.0:${PORT} -t public public/index.php"]
