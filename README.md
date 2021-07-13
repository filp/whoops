# whoops
PHP errors for cool kids

[![Total Downloads](https://img.shields.io/packagist/dm/filp/whoops.svg)](https://packagist.org/packages/filp/whoops)
[![Latest Version](http://img.shields.io/packagist/v/filp/whoops.svg)](https://packagist.org/packages/filp/whoops)
[![Build Status on newer versions](https://github.com/filp/whoops/workflows/Tests/badge.svg)](https://github.com/filp/whoops/actions?query=workflow%3ATests)
[![Build Status on older versions](https://travis-ci.org/filp/whoops.svg?branch=master)](https://travis-ci.org/filp/whoops)
[![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/filp/whoops/badges/quality-score.png?s=6225c36f2a2dd1fdca11ecc7b10b29105c8c62bd)](https://scrutinizer-ci.com/g/filp/whoops)
[![Code Coverage](https://scrutinizer-ci.com/g/filp/whoops/badges/coverage.png?s=711feb2069144d252d111b211965ffb19a7d09a8)](https://scrutinizer-ci.com/g/filp/whoops)

-----

![Whoops!](http://i.imgur.com/0VQpe96.png)

**whoops** is an error handler framework for PHP. Out-of-the-box, it provides a pretty
error interface that helps you debug your web projects, but at heart it's a simple yet
powerful stacked error handling system.

## Features

- Flexible, stack-based error handling
- Stand-alone library with (currently) no required dependencies
- Simple API for dealing with exceptions, trace frames & their data
- Includes a pretty rad error page for your webapp projects
- Includes the ability to [open referenced files directly in your editor and IDE](docs/Open%20Files%20In%20An%20Editor.md)
- Includes handlers for different response formats (JSON, XML, SOAP)
- Easy to extend and integrate with existing libraries
- Clean, well-structured & tested code-base

## Sponsors

<a href="https://blackfire.io/docs/introduction?utm_source=whoops&amp;utm_medium=github_readme&amp;utm_campaign=logo"><img src="https://i.imgur.com/zR8rsqk.png" alt="Blackfire.io" width="254" height="64"></a>

## Installing
If you use Laravel 4, Laravel 5.5+ or [Mezzio](https://docs.mezzio.dev/mezzio/), you already have Whoops. There are also community-provided instructions on how to integrate Whoops into
[Silex 1](https://github.com/whoops-php/silex-1),
[Silex 2](https://github.com/texthtml/whoops-silex),
[Phalcon](https://github.com/whoops-php/phalcon),
[Laravel 3](https://gist.github.com/hugomrdias/5169713#file-start-php),
[Laravel 5](https://github.com/GrahamCampbell/Laravel-Exceptions),
[CakePHP 3](https://github.com/dereuromark/cakephp-whoops/tree/cake3),
[CakePHP 4](https://github.com/dereuromark/cakephp-whoops),
[Zend 2](https://github.com/ghislainf/zf2-whoops),
[Zend 3](https://github.com/Ppito/zf3-whoops),
[Yii 1](https://github.com/igorsantos07/yii-whoops),
[FuelPHP](https://github.com/indigophp/fuel-whoops),
[Slim](https://github.com/zeuxisoo/php-slim-whoops/),
[Pimple](https://github.com/texthtml/whoops-pimple),
[Laminas](https://github.com/Ppito/laminas-whoops),
or any framework consuming [StackPHP middlewares](https://github.com/thecodingmachine/whoops-stackphp)
or [PSR-7 middlewares](https://github.com/franzliedke/whoops-middleware).

If you are not using any of these frameworks, here's a very simple way to install:

1. Use [Composer](http://getcomposer.org) to install Whoops into your project:

    ```bash
    composer require filp/whoops
    ```

1. Register the pretty handler in your code:

    ```php
    $whoops = new \Whoops\Run;
    $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
    $whoops->register();
    ```

For more options, have a look at the **example files** in [`examples/`](./examples) to get a feel for how things work. Also take a look at the [API Documentation](docs/API%20Documentation.md) and the list of available handlers below.

You may also want to override some system calls Whoops does. To do that, extend `Whoops\Util\SystemFacade`, override functions that you want and pass it as the argument to the `Run` constructor.

You may also collect the HTML generated to process it yourself:

```php
$whoops = new \Whoops\Run;
$whoops->allowQuit(false);
$whoops->writeToOutput(false);
$whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
$html = $whoops->handleException($e);
```

### Available Handlers

**whoops** currently ships with the following built-in handlers, available in the `Whoops\Handler` namespace:

- [`PrettyPageHandler`](https://github.com/filp/whoops/blob/master/src/Whoops/Handler/PrettyPageHandler.php) - Shows a pretty error page when something goes pants-up
- [`PlainTextHandler`](https://github.com/filp/whoops/blob/master/src/Whoops/Handler/PlainTextHandler.php) - Outputs plain text message for use in CLI applications
- [`CallbackHandler`](https://github.com/filp/whoops/blob/master/src/Whoops/Handler/CallbackHandler.php) - Wraps a closure or other callable as a handler. You do not need to use this handler explicitly, **whoops** will automatically wrap any closure or callable you pass to `Whoops\Run::pushHandler`
- [`JsonResponseHandler`](https://github.com/filp/whoops/blob/master/src/Whoops/Handler/JsonResponseHandler.php) - Captures exceptions and returns information on them as a JSON string. Can be used to, for example, play nice with AJAX requests.
- [`XmlResponseHandler`](https://github.com/filp/whoops/blob/master/src/Whoops/Handler/XmlResponseHandler.php) - Captures exceptions and returns information on them as a XML string. Can be used to, for example, play nice with AJAX requests.

You can also use pluggable handlers, such as [SOAP handler](https://github.com/whoops-php/soap).

## Authors

This library was primarily developed by [Filipe Dobreira](https://github.com/filp), and is currently maintained by [Denis Sokolov](https://github.com/denis-sokolov). A lot of awesome fixes and enhancements were also sent in by [various contributors](https://github.com/filp/whoops/contributors). Special thanks to [Graham Campbell](https://github.com/GrahamCampbell) and [Markus Staab](https://github.com/staabm) for continuous participation.
