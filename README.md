## Requirements

1. Composer

2. XAMPP (PHP and MySQL)

  

## Instructions

  

First, download or just run this command:

```

git clone https://www.github.com/Glovejuggler/PUPC-DDSS

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

**!! Important !!**
If thumbnail doesn't show but works (can be seen when the image is status 500 in the console):
1. Go to XAMPP's php.ini file
2. Change ;extension=gd(x) to extension=gd(x)

If it still doesn't work and the status in the console is 404
1. Add the port to the APP_URL in the .env (basically add :8000 at the end, example: http://localhost:8000)
  

Run the server:

```

php artisan serve

```