#!/bin/bash
# wait-for-db-dev.sh

echo "Waiting for MySQL to be ready..."

until php -r "
exit(@mysqli_connect(
    getenv('DB_HOST'),
    getenv('DB_USERNAME'),
    getenv('DB_PASSWORD'),
    getenv('DB_DATABASE')
) ? 0 : 1);
"; do
  echo "Waiting for MySQL to be ready..."
  sleep 2
done

echo "MySQL is ready!"

exec sh -c "php artisan migrate --force && php artisan db:seed --force && php artisan serve --host=0.0.0.0 --port=8000"
