# whoops
php errors for cool kids

[![Build Status](https://travis-ci.org/filp/whoops.png?branch=master)](https://travis-ci.org/filp/whoops) [![Total Downloads](https://poser.pugx.org/filp/whoops/downloads.png)](https://packagist.org/packages/filp/whoops)  [![Latest Stable Version](https://poser.pugx.org/filp/whoops/v/stable.png)](https://packagist.org/packages/filp/whoops)


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
Use [Composer](http://getcomposer.org) to install Whoops into your project:

```bash
composer require filp/whoops:1
```

Whoops can be easily integrated into many web frameworks.

If you use Laravel 4, you already have Whoops. For other frameworks,
see instructions on how to integrate Whoops into
[Silex](docs/Framework%20Integration.md#integrating-with-Silex),
[Phalcon](docs/Framework%20Integration.md#integrating-with-Phalcon),
[Laravel 3](https://gist.github.com/hugomrdias/5169713#file-start-php) (thanks, [@hugomrdias](https://github.com/hugomrdias)),
[CakePHP](https://github.com/oldskool/WhoopsCakephp) (thanks, [@oldskool](https://github.com/oldskool)),
[Zend Framework 2](docs/Framework%20Integration.md#integrating-with-Zend-Framework-2).

If you are not using any of these frameworks, have a look at the **example files** in `examples/` to get a feel for how things work. I promise it's really simple!

If you want to edit some more, take a look at the [API Documentation](docs/Framework%20Integration.md#API%20Documentation) and the list of available handers below.

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

