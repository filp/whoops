<?php

/**
 * Whoops - php errors for cool kids
 * @author Filipe Dobreira <http://github.com/filp>
 *
 * NOTE: Requires silex/silex, can be installed with composer:
 *
 * $ composer require silex/silex:*
 *
 * Run this example file with the PHP 5.4 web server with:
 *
 * $ cd project_dir
 * $ php -S localhost:8080
 *
 * and access localhost:8080/examples/example-silex.php through your browser
 *
 * Or just run it through apache/nginx/what-have-yous as usual.
 */


namespace Whoops\Example;

use RuntimeException;
use Silex\Application;
use Whoops\Provider\Silex\WhoopsServiceProvider;

require __DIR__ . '/../vendor/autoload.php';

$app = new Application();
$app['debug'] = true;

if ($app['debug']) {
    $app->register(new WhoopsServiceProvider());
}

$app->get('/', function () use ($app) {
    throw new RuntimeException("Oh no!");
});

$app->run();
