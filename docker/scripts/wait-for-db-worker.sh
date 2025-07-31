#!/bin/bash
# wait-for-db-worker.sh

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
  sleep 3
done

echo "MySQL is ready!"

# Wait for Redis to be ready
echo "Waiting for Redis to be ready..."
until redis-cli -h $REDIS_HOST -p $REDIS_PORT PING | grep -q "PONG"; do
  echo "Waiting for Redis to be ready... Retrying in 2 seconds."
  sleep 3
done

echo "Redis is ready!"

exec sh -c "php artisan queue:work redis --verbose --tries=3 --timeout=90"
