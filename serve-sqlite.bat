@echo off
php -d extension=pdo_sqlite -d extension=sqlite3 -S 127.0.0.1:8010 -t public public\index.php
