<?php
/**
 * damnit - php errors for cool kids
 * @author Filipe Dobreira <http://github.com/filp>
 */
require __DIR__ . '/vendor/autoload.php';

// Using DamnIt with a specific Handler:
$run     = new DamnIt\Run;
$handler = new DamnIt\Handler\BasicHandler;
$run->pushHandler($handler);
$run->register();

throw new RuntimeException("Hello!");