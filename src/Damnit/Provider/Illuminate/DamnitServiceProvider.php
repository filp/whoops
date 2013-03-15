<?php
/**
 * Damnit - php errors for cool kids
 * @author Filipe Dobreira <https://github.com/filp>
 */

namespace Damnit\Provider\Illuminate;
use Damnit\Handler\PrettyPageHandler;
use Damnit\Run;
use Illuminate\Support\ServiceProvider;

/**
 * Provides support for Illuminate (Laravel 4)
 * @author schickling <https://github.com/schickling>
 */
class DamnitServiceProvider extends ServiceProvider {

    /**
     * Register the service provider for damnit to laravel
     */
    public function register()
    {
        $app = $this->app;

        $app['damnit.handler'] = $app->share(function($app) {
            return new PrettyPageHandler;
        });

        $app['damnit'] = $app->share(function($app) {
            $run = new Run;

            return $run->pushHandler($app['damnit.handler']);
        });

        $app->error(function($e) use ($app) {
            $app['damnit']->handleException($e);
        });
    }
}