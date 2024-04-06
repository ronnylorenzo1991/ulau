# steak-salmon-analisys | Web module
## Requirements
- Node Package Manager >= 7.1 (`npm -v`)
- NodeJS >= 14.15.4 (`node -v`)
- PHP >= 8.2

## Installation

Clone repository.
```
$ git clone https://github.com/ronnylorenzo1991/steak-salmon-analisys-web
```

Install dependencies via composer.
```
$ composer install
```

Copy example environment configuration file to .env.
```
$ cp .env.example .env
```

Edit the .env file with your environment, database.
```
$ nano .env
```

Generate application key
```
$ php artisan key:generate
```

App Reset (Migrate and seed database)
```
$ php artisan app:reset
```

Install & Run Front End Tools (Require NodeJS >= 6.0.0)

```
$ npm install
$ npm run dev
```

## Plugins

## Tools
