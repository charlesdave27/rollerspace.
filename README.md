# 🚀 Laravel Project Setup (After Cloning)

## 1. Open project folder

cd your-project-folder

## 2. Install backend dependencies

composer install

## 3. Setup environment file

cp .env.example .env

## 4. Generate app key

php artisan key:generate

## 5. Configure .env

* Set database name
* Set username & password

Example:
DB_DATABASE=your_db
DB_USERNAME=root
DB_PASSWORD=

## 6. Create database

(Create it manually in MySQL / phpMyAdmin)

## 7. Run migrations

php artisan migrate

(Optional)
php artisan db:seed

## 8. Install frontend dependencies

npm install

## 9. Run frontend build

npm run dev

## 10. Start server

php artisan serve

## 11. Open in browser

http://127.0.0.1:8000

---

## 🛠️ Common Fixes

If something breaks:

php artisan config:clear
php artisan cache:clear
php artisan serve

---

## ✅ Requirements

* PHP
* Composer
* Node.js & npm
* MySQL (or other DB)

---

## 💡 Optional

php artisan storage:link
php artisan queue:work
