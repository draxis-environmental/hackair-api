# hackAIR REST API based on Lumen v5.3
## Description
Backend RESTful API based on Lumen v5.3 used for exchanging information with the [hackAIR web app](https://github.com/hackair-project/hackair-web) and the [hackAIR mobile app](https://github.com/hackair-project/hackair-mobile).

## Key libraries used:
* Dingo API package for Lumen (**[Dingo API](https://github.com/dingo/api)**)
* Laravel MongoDB package to provide support for MongoDB [Laravel MongoDB](**https://github.com/jenssegers/laravel-mongodb**)
* JSON Web Token Authentication package for Lumen [JWT Auth](**https://github.com/tymondesigns/jwt-auth**)
* Mandrill's API for transactional email as a service (**[Mandrill API](https://mandrillapp.com/api/docs/index.php.html)**)
* Sentry's integration with Laravel for exception and error alerts (**[Laravel Sentry](https://github.com/getsentry/sentry-laravel)**)
* Guzzle PHP HTTP client used for external API calls (**[Guzzle](https://github.com/guzzle/guzzle)**)

## Requirements
* PHP >= 5.6.4
* OpenSSL PHP Extension
* PDO PHP Extension
* Mbstring PHP Extension

## Installation instructions
* Clone project
* Install dependencies
```
composer install
```
* Update class autoloader
```
composer dump-autoload
```
* Setup permissions
```
chmod -R 775 /var/www/html/storage
chmod -R 775 /var/www/html/public
```
* Copy .env.example to .env and setup configurations
* Generate and add APP_KEY to .env file e.g. APP_KEY=lk7IqejFTEqaIep8guBE16Mg5JWpZtHj
* Run migrations & seeds
```
php artisan migrate --seed
```

## Documentation
Please refer to our online documentation for more information.
* [API Documentation](https://api.hackair.eu/docs/)
* [Download measurements / connect sensors endpoints](http://www.hackair.eu/docs/api/)

## License
The hackAIR API is open-sourced software licensed under the [GNU AGPLv3 license](https://opensource.org/licenses/AGPL-3.0)
