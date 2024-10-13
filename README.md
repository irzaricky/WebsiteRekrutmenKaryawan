# Recruitment-Project

> Website rekrutkmen karyawan mata kuliah pemograman backend

## Install and .env setup

1. npm install & composer install
2. php artisan key:generate

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

3. php artisan migrate

## Usage

```sh
npm run dev & php artisan serve
```

## Roadmap

<a href="https://roadmap.sh/r/roadmap-backend-optimalisasi-penerimaan-karyawan" target="_blank">Roadmap</a>

## Use Case dan activity diagram

<a href="https://drive.google.com/file/d/1-AWoY6_1C4c1hvLsW_3ePm5AMVJypLnb/view?usp=sharing" target="_blank">Draw.io</a>

## Design Web

<a href="https://www.figma.com/design/NlLjNJnHfkvCVbyHRJpklV/Arum-Maylan-Palupi's-team-library?node-id=0-1&t=HYeX4j0kkntqp5Ho-1">Figma version</a>
