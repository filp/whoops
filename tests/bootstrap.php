<?php
/**
 * Damnit - php errors for cool kids
 * @author Filipe Dobreira <http://github.com/filp>
 *
 * Bootstraper for PHPUnit tests.
 */
$loader = require_once __DIR__ . '/../vendor/autoload.php';
$loader->add('Damnit\\', __DIR__);
