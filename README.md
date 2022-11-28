<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://travis-ci.org/laravel/framework"><img src="https://travis-ci.org/laravel/framework.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## Url Short code 

Project Configuration:

- [Laravel 9](https://laravel.com/docs/routing).
- [jenssegers/agent](https://github.com/jenssegers/agent).
- [php8](https://www.php.net/).
- [darkaonline/l5-swagger](https://github.com/DarkaOnLine/L5-Swagger).
- Database  Mysql.


- Please copy .env.example to .env and run following commands to ignition start project.
- Please check mysql database DB_USERNAME and DB_PASSWORD in .env with your mysql database username password. It is batter that you create database with the same name DB_DATABASE of .env file

## Please run following commands for ignition start  
- **sudo chmod -R 777 storage/logs/ storage/framework/ bootstrap/cache**
- **composer install**
- **php artisan migrate**
- **php artisan db:seed**
- **php artisan l5-swagger:generate**

## Check Project
- [Click Here! You can now check project swagger from following apis](http://localhost/url-shorcode/public/api/documentation).
- **Admin emailid and password**
- email: admin@project.com .
- Password: Admin@1234 .

## Apis are self describing but still I am adding description here
- By login api admin can get access token to perform crud operation of short url functionality
- Refresh token api is used to refresh token at admin side
- List, store, details and update apis are for crud operation and can be used by admin only.
- {hash} api is used for redirecting user to actual url.

## Validation for create and update
- Only one short url for one link can be created once it is used you can't create multiple codes for the same url.
- Once code used you can create new short url for the same url. 
- Once short code used it can't be used 2nd time it will give validation error.

## License


The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Happy coding