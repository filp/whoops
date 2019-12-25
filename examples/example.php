<?php
/**
 * Whoops - php errors for cool kids
 * @author Filipe Dobreira <http://github.com/filp>
 *
 * Run this example file with the PHP 5.4 web server with:
 *
 * $ cd project_dir
 * $ php -S localhost:8080
 *
 * and access localhost:8080/examples/example.php through your browser
 *
 * Or just run it through apache/nginx/what-have-yous as usual.
 */

namespace Whoops\Example;

use Exception as BaseException;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/lib.php';

class Exception extends BaseException
{
}

$run     = new Run();
$handler = new PrettyPageHandler();

// Add a custom table to the layout:
$handler->addDataTable('Ice-cream I like', [
    'Chocolate' => 'yes',
    'Coffee & chocolate' => 'a lot',
    'Strawberry & chocolate' => 'it\'s alright',
    'Vanilla' => 'ew',
]);

$handler->setApplicationPaths([__FILE__]);

$handler->addDataTableCallback('Details', function(\Whoops\Exception\Inspector $inspector) {
    $data = array();
    $exception = $inspector->getException();
    if ($exception instanceof SomeSpecificException) {
        $data['Important exception data'] = $exception->getSomeSpecificData();
    }
    $data['Exception class'] = get_class($exception);
    $data['Exception code'] = $exception->getCode();
    return $data;
});

$run->pushHandler($handler);

// Example: tag all frames inside a function with their function name
$run->pushHandler(function ($exception, $inspector, $run) {

    $inspector->getFrames()->map(function ($frame) {

        if ($function = $frame->getFunction()) {
            $frame->addComment("This frame is within function '$function'", 'cpt-obvious');
        }

        return $frame;
    });

});

$run->register();

function fooBar()
{
    throw new Exception("Something broke!");
}

function bar()
{
    whoops_add_stack_frame(function(){
        fooBar();
    });
}

bar();
