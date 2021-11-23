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
