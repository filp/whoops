<?php
App::import('Vendor', 'filp/whoops/src/Whoops/Run');
App::import('Vendor', 'filp/whoops/src/Whoops/Exception/Frame');
App::import('Vendor', 'filp/whoops/src/Whoops/Exception/FrameCollection');
App::import('Vendor', 'filp/whoops/src/Whoops/Exception/Inspector');
App::import('Vendor', 'filp/whoops/src/Whoops/Handler/HandlerInterface');
App::import('Vendor', 'filp/whoops/src/Whoops/Handler/Handler');
App::import('Vendor', 'filp/whoops/src/Whoops/Handler/PrettyPageHandler');

/**
 * The WhoopsExceptionHandler will pass our Exception to the Whoops library.
 * Exceptions will then be shown on a "Pretty Page", including the code excerpt
 * where the Exception was thrown along with an interactive stack trace.
 *
 * @author Jan Dorsman, ODC Engineering <jan.dorsman@odc-engineering.nl>
 * @copyright Copyright (c) 2014, ODC Engineering
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */
class WhoopsExceptionHandler {

/**
 * This integer sets the minimum debug level required.
 *
 * If your CakePHP debug level is lower than this integer, the default ExceptionHandler is used.
 * The Whoops library provides a lot of information that you'd normally not want to share with
 * your end users (like code excerpts and server details). So make sure not to set this value too low.
 *
 * @var int
 */
	public static $minDebugLevel = 1;

/**
 * This method will handle any Exception thrown.
 *
 * @param Exception $exception The exception to be handled
 *
 * @return void
 */
	public static function handle($exception) {
		// Verify if the debug level is at least the minDebugLevel
		if (Configure::read('debug') >= self::$minDebugLevel) {
			// Debug level is high enough, use the Whoops handler
			$Whoops = new Whoops\Run();
			$Whoops->pushHandler(new Whoops\Handler\PrettyPageHandler());
			$Whoops->handleException($exception);
		} else {
			// Debug level is too low, fall back to CakePHP default ErrorHandler
			ErrorHandler::handleException($exception);
		}
	}

}

