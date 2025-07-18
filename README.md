Добавила докер для удобства локальной разработки. 

Развернуть докер:

cd docker_s
docker-compose up -d
Зайти в контейнер: docker-compose exec php-fpm bash
В контейнере: composer install
Скопировать .env.example из корня и вставить в .env
Запустить миграции:

В контейнере запустить команды php artisan migrate и php artisan db:seed
Сайт доступен по адресу: http://localhost:81
