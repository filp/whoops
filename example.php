<?php
/**
 * Damnit - php errors for cool kids
 * @author Filipe Dobreira <http://github.com/filp>
 *
 * Run this example file with the PHP 5.4 web server with:
 *
 * $ cd project_dir
 * $ php -S localhost:8080
 *
 * and access localhost:8080/example.php through your browser
 *
 * Or just run it through apache/nginx/what-have-yous as usual.
 */

namespace Damnit\Example;
use Damnit\Run;
use Damnit\Handler\PrettyPage;
use Exception as BaseException;

require __DIR__ . '/vendor/autoload.php';

class Exception extends BaseException {}

$run = new Run;
$run->pushHandler(new PrettyPage);
$run->register();

function fooBar() {
    throw new Exception("Something broke!");
}

function bar()
{
    fooBar();
}

bar();
