<?php
/**
 * Whoops - php errors for cool kids
 * @author Filipe Dobreira <https://github.com/filp>
 */

namespace Whoops\Provider\Illuminate;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;
use Illuminate\Support\ServiceProvider;

/**
 * Provides support for Illuminate (Laravel 4)
 * @author schickling <https://github.com/schickling>
 */
class WhoopsServiceProvider extends ServiceProvider {

    /**
     * Register the service provider for whoops to laravel
     */
    public function register()
    {
        $app = $this->app;

        $app['whoops.handler'] = $app->share(function($app) {
            return new PrettyPageHandler;
        });

        $app['whoops'] = $app->share(function($app) {
            $run = new Run;

            return $run->pushHandler($app['whoops.handler']);
        });

        if ($app['config']->get('app.debug')) {
            $app->error(function($e) use ($app) {
                $app['whoops']->handleException($e);
            });
        }
    }
}
