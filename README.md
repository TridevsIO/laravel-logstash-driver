# laravel-logstash-driver

A Driver to connect laravel logs to logstash

## Installation

You can install this package via composer using this command:

```
composer require tridevsio/laravel-logstash-driver
```

The package will automatically register itself.

Add new log channel to `config/logging.php` under the section **channels**:

```php
   'logstash' => [
        'driver' => 'custom',
        'appName' => env('APP_NAME', 'My test app'),
        'via'    => \TridevsIO\LaravelLogstashDriver\LogstashLogger::class,
        'host'   => env('LOGSTASH_HOST', '127.0.0.1'),
        'port'   => env('LOGSTASH_PORT', 4718),
        'extra'   => [],
    ],
```

### Optional Config

Optionally add some extra data like the user id currently logged in

```php
'logstash' => [
    'extra'   => [
       'user_id' => auth()->user() ? auth()->user()->id : null, 
    ],
],
```