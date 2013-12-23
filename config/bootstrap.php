<?php

namespace Whoops;

use Whoops\Handler\PrettyPageHandler;

if (PHP_SAPI != 'cli') {
	require __DIR__ . '/../Exception/ErrorException.php';
	require __DIR__ . '/../Exception/Frame.php';
	require __DIR__ . '/../Exception/FrameCollection.php';
	require __DIR__ . '/../Exception/Inspector.php';
	require __DIR__ . '/../Handler/HandlerInterface.php';
	require __DIR__ . '/../Handler/Handler.php';
	require __DIR__ . '/../Handler/CallbackHandler.php';

	require __DIR__ . '/../Handler/JsonResponseHandler.php';
	require __DIR__ . '/../Handler/PrettyPageHandler.php';
	require __DIR__ . '/../Handler/XmlResponseHandler.php';

	require __DIR__ . '/../Util/TemplateHelper.php';

	require __DIR__ . '/../Provider/Silex/WhoopsServiceProvider.php';

	require __DIR__ . '/../run.php';
	$run     = new Run;
	$handler = new PrettyPageHandler;
	$run->pushHandler(new PrettyPageHandler);   

	$run->register();
}


 