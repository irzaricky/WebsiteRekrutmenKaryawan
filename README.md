# Recruitment-Project

> Website rekrutkmen karyawan mata kuliah pemograman backend

## Install and .env setup

1. npm install & composer install


## Database setup

1. Buat database pada mysql sesuai dengan env
2. Modified file .env

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel_db_rekrutmen
DB_USERNAME=root
DB_PASSWORD=
```

3. php artisan key:generate
4. php artisan migrate --seed
5. php artisan storage:link

## Usage

```sh
npm run dev & php artisan serve
```
