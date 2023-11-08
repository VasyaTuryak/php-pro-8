# php-pro-8

1. створити в корені папку public або www і в середені файл app.php або index.php який буде вашим фронт-контролером

2. в docker-compose прописати додавання контейнеру nginx (зразок нижче)

nginx:
  image: nginx:latest
  container_name: nginx_${PROJECT_NAME}
  ports:
    - ${HTTP_PORT}:80
    - ${HTTPS_PORT}:443
  volumes:
    - ./:${WORKDIR}
    - ./docker/configs/nginx:/etc/nginx/conf.d
    - ./var/logs/nginx:/var/log/nginx
  links:
    - php
3. створити конфігурацію для налаштування вебсерверу (зразок нижче)

server {
    index index.php;
    error_log  /var/log/nginx/error.log;
    access_log /var/log/nginx/access.log;
    root /srv/src/app/public;

    location / {
        try_files $uri /index.php$is_args$args;
    }

    location ~ index\.php$ {
            include fastcgi_params;
            fastcgi_pass php:9000;
            fastcgi_index index.php;
            fastcgi_param SCRIPT_FILENAME $document_root/$fastcgi_script_name;
    }
}
4. виконати команду docker-compose up 

5. на 80 балів достатньо вивести у фронт-контролері "Hello world", до 94 балів ви можете заробити, реалізувавши у фронт-контролері логіку обчислення, яку ви робили раніше (калькулятор або скорочувач url) хардкодом

6. додаткові бали ви можете отримати якщо зробите прийом даних з браузера через $_GET

наприклад, якщо функціонал скорочування урла, то саме урл маємо прийняти так http://localhost?url=https://site.com
