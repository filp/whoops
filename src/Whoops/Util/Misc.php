<?php
/**
 * Whoops - php errors for cool kids
 * @author Filipe Dobreira <http://github.com/filp>
 */

namespace Whoops\Util;

class Misc
{
	/**
	 * Can we at this point in time send HTTP headers?
	 *
	 * Currently this checks if we are even serving an HTTP request,
	 * as opposed to running from a command line.
	 *
	 * If we are serving an HTTP request, we check if it's not too late.
	 *
	 * @return bool
	 */
	public static function canSendHeaders()
	{
		return isset($_SERVER["REQUEST_URI"]) && !headers_sent();
	}
}
