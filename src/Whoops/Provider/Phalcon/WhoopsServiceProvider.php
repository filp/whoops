<?php
/**
 * Whoops - php errors for cool kids
 * @author Filipe Dobreira <http://github.com/filp>
 */

namespace Whoops\Provider\Phalcon;

use Whoops\Run;
use Whoops\Handler\PrettyPageHandler;
use Phalcon\DI;
use Phalcon\DI\Exception;

class WhoopsServiceProvider
{
    /**
     * @see Silex\ServiceProviderInterface::register
     * @param  Silex\Application $app
     */
    public function __construct(DI $di = null)
    {
        if (!$di) {
            $di = DI::getDefault();
        }

        // There's only ever going to be one error page...right?
        $di->setShared('whoops.error_page_handler', function() {
            return new PrettyPageHandler;
        });

        // Retrieves info on the Phalcon environment and ships it off
        // to the PrettyPageHandler's data tables:
        // This works by adding a new handler to the stack that runs
        // before the error page, retrieving the shared page handler
        // instance, and working with it to add new data tables
        $phalcon_info_handler = function() use($di) {
            try {
                $request = $di['request'];
            } catch (Exception $e) {
                // This error occurred too early in the application's life
                // and the request instance is not yet available.
                return;
            }

            // Request info:
            $di['whoops.error_page_handler']->addDataTable('Phalcon Application (Request)', array(
                'URI'         => $request->getScheme().'://'.$request->getServer('HTTP_HOST').$request->getServer('REQUEST_URI'),
                'Request URI' => $request->getServer('REQUEST_URI'),
                'Path Info'   => $request->getServer('PATH_INFO'),
                'Query String'=> $request->getServer('QUERY_STRING') ?: '<none>',
                'HTTP Method' => $request->getMethod(),
                'Script Name' => $request->getServer('SCRIPT_NAME'),
                //'Base Path'   => $request->getBasePath(),
                //'Base URL'    => $request->getBaseUrl(),
                'Scheme'      => $request->getScheme(),
                'Port'        => $request->getServer('SERVER_PORT'),
                'Host'        => $request->getServerName(),
            ));
        };

        $di->setShared('whoops', function() use($di, $phalcon_info_handler) {
            $run = new Run;
            $run->pushHandler($di['whoops.error_page_handler']);
            $run->pushHandler($phalcon_info_handler);
            return $run;
        });

        $di['whoops']->register();
    }
}
