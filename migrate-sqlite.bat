@echo off
if not exist database\database.sqlite type nul > database\database.sqlite
php -d extension=pdo_sqlite -d extension=sqlite3 artisan migrate:fresh --seed
