<?php
/**
 * Damnit - php errors for cool kids
 * @author Filipe Dobreira <http://github.com/filp>
 *
 * NOTE: Requires silex/silex, can be installed with composer
 * within this project using the --dev flag:
 *
 * $ composer install --dev
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
require __DIR__ . '/../vendor/autoload.php';

use Damnit\Silex\DamnitServiceProvider;
use Silex\Application;

$app = new Application;
$app['debug'] = true;

if($app['debug']) {
    $app->register(new DamnitServiceProvider);

    // Example: Extend the error page to disable the 'damnit' branding
    // in the top-right corner, so those big-wigs at the office wont'
    // throw a tantrum:
    $app['damnit.error_page_handler'] = $app->extend('damnit.error_page_handler', function($handler) {
        $handler->showBranding(true);
        return $handler;
    });
}

$app->get('/', function() use($app) {
    throw new RuntimeException("Oh no!");
});

$app->run();
