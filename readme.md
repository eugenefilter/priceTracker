**FakeShop**
Небольшой Laravel-проект для демонстрации магазина с фейковыми товарами.

## Установка

```bash
git 

# Установить зависимости
composer install
npm install

# Скопировать и настроить .env
cp .env.example .env

# Заполнить переменные ниже
```

## Переменные окружения (.env)

```dotenv
# Pusher
PUSHER_APP_ID=12345
PUSHER_APP_KEY=pk-abcdef
PUSHER_APP_SECRET=sk-uvwxyz
PUSHER_HOST=127.0.0.1
PUSHER_PORT=6001
PUSHER_SCHEME=http
PUSHER_APP_CLUSTER=mt1

# Vite (import.meta.env)
VITE_PUSHER_KEY=pk-abcdef
VITE_PUSHER_CLUSTER=mt1
VITE_WS_HOST=127.0.0.1
VITE_WS_PORT=8443
VITE_PUSHER_SCHEME=http

# База данных
DB_CONNECTION=sqlite

# Трансляция и очередь
BROADCAST_CONNECTION=log
BROADCAST_DRIVER=pusher
QUEUE_CONNECTION=sync
```

## База данных

```bash
# Запустить миграции
php artisan migrate

# Would you like to create it?
Выбрать Yes

# Заполнить тестовыми товарами
php artisan db:seed --class="FakeShop\\Database\\Seeders\\FakeProductSeeder"
```

# Выполимте команды

php artisan key:generate

php artisan config:clear
php artisan config:cache

## Сборка фронтенда

```bash
# Сборка для продакшена
npm run build
```

## Запуск сервера

```bash
# Запуск WS-сервера
php websocket-server.php
```

## Парсер ссылок

```bash
php artisan links:parse
```

---
