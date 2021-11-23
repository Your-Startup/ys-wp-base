Создаем проект в папке stihi

Переходим в нее, если нахожимся не в ней.
<pre><code>cd D:\Work\stihi</code></pre>
Скачиваем Wordpress
<pre><code>wp core download --locale=ru_RU</code></pre>

Скачиваем репозиторий с gitHub (должен быть настроен ssh ключ)
<pre><code>
git init
git remote add origin git@github.com:Your-Startup/stihi.git
git fetch --all
git checkout main
</code></pre>

Устанавливаем связь с бд и создаем ее
<pre><code>wp config create
wp db create
</code></pre>

Импортируем тестовую базу данных
<pre><code>wp db import ys-db-dump.sql</code></pre>

Если все хорошо, то получится такая директория и такие ответы на команды
![image](https://user-images.githubusercontent.com/54105539/143086694-4d8c7e2e-d67c-4c12-9447-93568323be56.png)

Если имя папки проекта называется по другому, нужно заменить ссылки в базе данных
Где project-new имя вашего проекта
<pre><code>wp search-replace localhost/project-new localhost/stihi --all-tables --network --report-changed-only</code></pre>

![image](https://user-images.githubusercontent.com/54105539/143086990-38db42d7-ca22-4700-86fe-2a4ce95d3561.png)
