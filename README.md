Добавила докер для удобства локальной разработки. 

# Развернуть докер:

1) cd docker_s
2) docker-compose up -d
3) Зайти в контейнер: docker-compose exec php-fpm bash
4) В контейнере: composer install
5) Скопировать .env.example из корня и вставить в .env
6) Запустить миграции:
В контейнере запустить команды php artisan migrate и php artisan db:seed

# Сайт доступен по адресу: http://localhost:81


# Список доступных роутов:

GET|HEAD  api/orders 

POST      api/orders 

PUT       api/orders/{order} 

POST      api/orders/{order}/cancel

POST      api/orders/{order}/complete

POST      api/orders/{order}/resume 

GET|HEAD  api/products

GET|HEAD  api/stock_movements

GET|HEAD  api/warehouses 
