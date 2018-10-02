<?php

require_once __DIR__ . '/../vendor/autoload.php';

try {
    (new Dotenv\Dotenv(__DIR__ . '/../'))->load();
} catch (Dotenv\Exception\InvalidPathException $e) {
    //
}

/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
|
| Here we will load the environment and create the application instance
| that serves as the central piece of this framework. We'll use this
| application as an "IoC" container and router for this framework.
|
*/

$app = new Laravel\Lumen\Application(
    realpath(__DIR__ . '/../')
);

// Define path.config needed for Google Maps library.
$app->instance('path.config', app()->basePath() . DIRECTORY_SEPARATOR . 'config');
$app->instance('path.storage', app()->basePath() . DIRECTORY_SEPARATOR . 'storage');

$app->withFacades(true, [
    'Illuminate\Support\Facades\Redirect' => 'Redirect',
    'Illuminate\Support\Facades\Hash' => 'Hash'
]);
$app->bind('redirect', 'Laravel\Lumen\Http\Redirector');

/*
|--------------------------------------------------------------------------
| Register Container Bindings
|--------------------------------------------------------------------------
|
| Now we will register a few bindings in the service container. We will
| register the exception handler and the console kernel. You may add
| your own bindings here if you like or you can make another file.
|
*/

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    App\Console\Kernel::class
);

// Register configs
$app->configure('filesystems');
$app->configure('googlemaps');

// Register class aliases
if (!class_exists('Config')) {
    class_alias('Illuminate\Support\Facades\Config', 'Config');
}
if (!class_exists('Storage')) {
    class_alias('Illuminate\Support\Facades\Storage', 'Storage');
}
if (!class_exists('Sentry')) {
    class_alias('Sentry\SentryLaravel\SentryFacade', 'Sentry');
}
if (!class_exists('Image')) {
    class_alias('Intervention\Image\Facades\Image', 'Image');
}
if (!class_exists('GoogleMaps')) {
    class_alias('GoogleMaps\Facade\GoogleMapsFacade', 'GoogleMaps');
}

$app->singleton(
    Illuminate\Contracts\Filesystem\Factory::class,
    function ($app) {
        return new Illuminate\Filesystem\FilesystemManager($app);
    }
);

/*
|--------------------------------------------------------------------------
| Register Middleware
|--------------------------------------------------------------------------
|
| Next, we will register the middleware with the application. These can
| be global middleware that run before and after each request into a
| route or middleware that'll be assigned to some specific routes.
|
*/



$app->middleware([
    App\Http\Middleware\RequestLogMiddleware::class,
    App\Http\Middleware\Language::class
]);

$app->routeMiddleware([
    'auth' => App\Http\Middleware\Authenticate::class,
    'auth.arduino' => App\Http\Middleware\AuthenticateArduinoSensor::class,
    'lang' => App\Http\Middleware\Language::class,
    'cors' => \Barryvdh\Cors\HandleCors::class
]);

/*
|--------------------------------------------------------------------------
| Register Service Providers
|--------------------------------------------------------------------------
|
| Here we will register all of the application's service providers which
| are used to bind services into the container. Service providers are
| totally optional, so you are not required to uncomment this line.
|
*/

$app->register(App\Providers\AppServiceProvider::class);
$app->register(App\Providers\AuthServiceProvider::class);
$app->register(Barryvdh\Cors\LumenServiceProvider::class);
$app->register(Jenssegers\Mongodb\MongodbServiceProvider::class);
$app->register(Dingo\Api\Provider\LumenServiceProvider::class);
$app->register(App\Providers\EventServiceProvider::class);
$app->register(Sentry\SentryLaravel\SentryLumenServiceProvider::class);
$app->register(Intervention\Image\ImageServiceProviderLumen::class);
$app->register(GoogleMaps\ServiceProvider\GoogleMapsServiceProvider::class);
$app->register(HighSolutions\LangImportExport\LangImportExportServiceProvider::class);
$app->register(Dimsav\Translatable\TranslatableServiceProvider::class);

$app->configure('cors');
$app->configure('geocoder');
$app->configure('mail');
$app->configure('services');

// putting this here cause of mongo service provider requirement
$app->withEloquent();

/*
|--------------------------------------------------------------------------
| Load The Application Routes
|--------------------------------------------------------------------------
|
| Next we will include the routes file so that they can all be added to
| the application. This will provide all of the URLs the application
| can respond to, as well as the controllers that may handle them.
|
*/

$app->group(['namespace' => 'App\Http\Controllers'], function ($app) {
    require __DIR__ . '/../routes/web.php';
});

app('translator')->setLocale('en');

return $app;
