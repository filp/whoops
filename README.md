# whoops
php errors for cool kids

[![Total Downloads](https://img.shields.io/packagist/dm/filp/whoops.svg)](https://packagist.org/packages/filp/whoops)
[![Latest Version](http://img.shields.io/packagist/v/filp/whoops.svg)](https://packagist.org/packages/filp/whoops)
[![Build Status](https://travis-ci.org/filp/whoops.svg?branch=master)](https://travis-ci.org/filp/whoops)
[![Dependency Status](https://depending.in/filp/whoops.png)](https://depending.in/filp/whoops)
[![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/filp/whoops/badges/quality-score.png?s=6225c36f2a2dd1fdca11ecc7b10b29105c8c62bd)](https://scrutinizer-ci.com/g/filp/whoops)
[![Code Coverage](https://scrutinizer-ci.com/g/filp/whoops/badges/coverage.png?s=711feb2069144d252d111b211965ffb19a7d09a8)](https://scrutinizer-ci.com/g/filp/whoops)

-----

![Whoops!](http://i.imgur.com/xiZ1tUU.png)

**whoops** is an error handler base/framework for PHP. Out-of-the-box, it provides a pretty
error interface that helps you debug your web projects, but at heart it's a simple yet
powerful stacked error handling system.

## (current) Features

- Flexible, stack-based error handling
- Stand-alone library with (currently) no required dependencies
- Simple API for dealing with exceptions, trace frames & their data
- Includes a pretty rad error page for your webapp projects
- Includes the ability to [open referenced files directly in your editor and IDE](docs/Open%20Files%20In%20An%20Editor.md)
- Includes handlers for different response formats (JSON, XML, SOAP)
- Includes a Silex Service Provider for painless integration with [Silex](http://silex.sensiolabs.org/)
- Includes a Phalcon Service Provider for painless integration with [Phalcon](http://phalconphp.com/)
- Includes a Module for equally painless integration with [Zend Framework 2](http://framework.zend.com/)
- Easy to extend and integrate with existing libraries
- Clean, well-structured & tested code-base

## Installing
If you use Laravel 4, you already have Whoops. There are also community-provided instructions on how to integrate Whoops into
[Silex](docs/Framework%20Integration.md#integrating-with-silex),
[Phalcon](docs/Framework%20Integration.md#integrating-with-phalcon),
[Laravel 3](https://gist.github.com/hugomrdias/5169713#file-start-php),
[CakePHP](https://github.com/oldskool/WhoopsCakephp),
[Zend Framework 2](https://github.com/ghislainf/zf2-whoops),
[Yii 1](https://github.com/igorsantos07/yii-whoops).

If you are not using any of these frameworks, here's a very simple way to install:

1. Use [Composer](http://getcomposer.org) to install Whoops into your project:

    ```bash
    composer require filp/whoops:1
    ```

1. Register the pretty handler in your code:

    ```php
    $whoops = new \Whoops\Run;
    $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
    $whoops->register();
    ```

For more options, have a look at the **example files** in `examples/` to get a feel for how things work. Also take a look at the [API Documentation](docs/API%20Documentation.md) and the list of available handers below.

### Available Handlers

**whoops** currently ships with the following built-in handlers, available in the `Whoops\Handler` namespace:

- [`PrettyPageHandler`](https://github.com/filp/whoops/blob/master/src/Whoops/Handler/PrettyPageHandler.php) - Shows a pretty error page when something goes pants-up
- [`CallbackHandler`](https://github.com/filp/whoops/blob/master/src/Whoops/Handler/CallbackHandler.php) - Wraps a closure or other callable as a handler. You do not need to use this handler explicitly, **whoops** will automatically wrap any closure or callable you pass to `Whoops\Run::pushHandler`
- [`JsonResponseHandler`](https://github.com/filp/whoops/blob/master/src/Whoops/Handler/JsonResponseHandler.php) - Captures exceptions and returns information on them as a JSON string. Can be used to, for example, play nice with AJAX requests.
- [`XmlResponseHandler`](https://github.com/filp/whoops/blob/master/src/Whoops/Handler/XmlResponseHandler.php) - Captures exceptions and returns information on them as a XML string. Can be used to, for example, play nice with AJAX requests.
- [`SoapResponseHandler`](https://github.com/filp/whoops/blob/master/src/Whoops/Handler/SoapResponseHandler.php) - Captures exceptions and returns information on them as a SOAP string. Might be used for SOAP Webservices.

## Authors

This library was primarily developed by [Filipe Dobreira](https://github.com/filp), and is currently maintained by [Denis Sokolov](https://github.com/denis-sokolov). A lot of awesome fixes and enhancements were also sent in by [various contributors](https://github.com/filp/whoops/contributors).


[![Bitdeli Badge](https://d2weczhvl823v0.cloudfront.net/filp/whoops/trend.png)](https://bitdeli.com/free "Bitdeli Badge")
