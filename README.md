# AvoCato Sushi — Laravel + Frontend + Admin

Це готовий пакет кастомних файлів для чистого Laravel 12 проєкту.

Усередині вже об'єднані:

- Blade-верстка AvoCato;
- CSS + JS через Vite;
- вибір міста Переяслав / Березань;
- Laravel API для меню;
- товари та категорії з MySQL;
- кошик у localStorage;
- адмінка продуктів;
- додавання / редагування / видалення;
- завантаження фото;
- окремі ціни та доступність по містах.

## Як встановити

```bash
composer create-project laravel/laravel avocato
cd avocato

composer require laravel/breeze --dev
php artisan breeze:install blade
php artisan install:api
npm install
```

Після цього скопіюйте весь вміст цього архіву в корінь `avocato/` із заміною файлів.

Налаштуйте `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=avocato
DB_USERNAME=root
DB_PASSWORD=
```

Потім:

```bash
php artisan migrate
php artisan db:seed
php artisan storage:link
```

Запуск у двох терміналах:

```bash
php artisan serve
```

```bash
npm run dev
```

Сайт:

http://127.0.0.1:8000

Для першого адміністратора відкрийте:

http://127.0.0.1:8000/register

Адмінка:

http://127.0.0.1:8000/admin/products

API:

- `/api/cities`
- `/api/categories`
- `/api/menu/pereiaslav`
- `/api/menu/berezan`

## Важливо

Після `db:seed` створяться міста та категорії. Продукти додаються через адмінку. Після додавання продукт одразу приходить на фронт через API.

Кнопка `Оформити замовлення` поки не створює order у БД — це наступний модуль (`orders`, `order_items`, checkout form).
