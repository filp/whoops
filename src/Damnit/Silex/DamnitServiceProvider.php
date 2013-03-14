<?php
/**
 * Damnit - php errors for cool kids
 * @author Filipe Dobreira <http://github.com/filp>
 */

namespace Damnit\Silex;
use Damnit\Run;
use Damnit\Handler\PrettyPageHandler;
use Silex\ServiceProviderInterface;
use Silex\Application;

class DamnitServiceProvider implements ServiceProviderInterface
{
    /**
     * @see Silex\ServiceProviderInterface::register
     * @param  Silex\Application $app
     */
    public function register(Application $app)
    {
        // There's only ever going to be one error page...right?
        $app['damnit.error_page'] = $app->share(function() {
            return new PrettyPageHandler;
        });

        $app['damnit'] = $app->share(function() use($app) {
            $run = new Run;
            $run->pushHandler($app['damnit.error_page']);
            return $run;
        });

        $app->error(array($app['damnit'], Run::EXCEPTION_HANDLER));
        $app['damnit']->register();
    }

    /**
     * @see Silex\ServiceProviderInterface::boot
     */
    public function boot(Application $app) {}
}
