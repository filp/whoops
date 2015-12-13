<?php
/**
 * Whoops - php errors for cool kids
 * @author Filipe Dobreira <http://github.com/filp>
 *
 * Bootstraper for PHPUnit tests.
 */
error_reporting(E_ALL | E_STRICT);
// Can be required more than once to allow running
// phpunit installed with Composer
// http://stackoverflow.com/a/12798022
$loader = require __DIR__ . '/../vendor/autoload.php';
$loader->add('Whoops\\', __DIR__);
