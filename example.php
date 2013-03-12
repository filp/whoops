<?php
/**
 * Damnit - php errors for cool kids
 * @author Filipe Dobreira <http://github.com/filp>
 */
require __DIR__ . '/vendor/autoload.php';

// Using Damnit with a specific Handler:
$run     = new Damnit\Run;
$handler = new Damnit\Handler\BasicHandler;
$run->pushHandler($handler);
$run->register();

throw new RuntimeException("Hello!");
