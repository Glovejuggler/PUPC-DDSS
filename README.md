## Requirements
1. Composer
2. XAMPP (PHP and MySQL)

## Instructions

First, download or just run this command:
```
git clone https:://www.github.com/Glovejuggler/PUPC-DDSS
```

Then, `cd` to the app:
```
cd PUPC-DDSS
```

After that, run these commands:
```
composer install
```
```
cp .env.example .env
```
```
php artisan key:generate
```
```
php artisan migrate --seed
```

To run the server:
```
php artisan serve
```