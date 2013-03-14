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
        $app['damnit.error_page_handler'] = $app->share(function() {
            return new PrettyPageHandler;
        });

        // Retrieves info on the Silex environment and ships it off
        // to the PrettyPageHandler's data tables:
        // This works by adding a new handler to the stack that runs
        // before the error page, retrieving the shared page handler
        // instance, and working with it to add new data tables
        $app['damnit.silex_info_handler'] = $app->protect(function() use($app) {
            $request = $app['request'];

            // General application info:
            $app['damnit.error_page_handler']->addDataTable('Silex Application', array(
                'Charset'          => $app['charset'],
                'Locale'           => $app['locale'],
                'Route Class'      => $app['route_class'],
                'Dispatcher Class' => $app['dispatcher_class'],
                'Application Class'=> get_class($app)
            ));

            // Request info:
            $app['damnit.error_page_handler']->addDataTable('Silex Application (Request)', array(
                'URI'         => $request->getUri(),
                'Request URI' => $request->getRequestUri(),
                'Base URL'    => $request->getBaseUrl(),
                'Path Info'   => $request->getPathInfo(),
                'Query String'=> $request->getQueryString() ?: '<none>',
                'HTTP Method' => $request->getMethod(),
                'Script Name' => $request->getScriptName(),
                'Base Path'   => $request->getBasePath(),
                'Base URL'    => $request->getBaseUrl(),
                'Scheme'      => $request->getScheme(),
                'Port'        => $request->getPort(),
                'Host'        => $request->getHost(),
            ));
        });

        $app['damnit'] = $app->share(function() use($app) {
            $run = new Run;
            $run->pushHandler($app['damnit.error_page_handler']);
            $run->pushHandler($app['damnit.silex_info_handler']);
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
