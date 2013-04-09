# whoops
php errors for cool kids

[![Build Status](https://travis-ci.org/filp/whoops.png?branch=master)](https://travis-ci.org/filp/whoops)

-----

![Whoops!](http://i.imgur.com/xmMXzRA.png)

**whoops** is an error handler base/framework for PHP. Out-of-the-box, it provides a pretty
error interface that helps you debug your web projects, but at heart it's a simple yet
powerful stacked error handling system.

## (current) Features

- Flexible, stack-based error handling
- Stand-alone library with (currently) no required dependencies
- Simple API for dealing with exceptions, trace frames & their data
- Includes a pretty rad error page for your webapp projects
- Includes a `Silex\WhoopsServiceProvider` for painless integration with [Silex](http://silex.sensiolabs.org/)
- Includes an `Illuminate\WhoopsServiceProvider` for equally painless integration with [Laravel 4](http://laravel.com/)
- Easy to extend and integrate with existing libraries
- Clean, well-structured & tested code-base (well, except `pretty-template.php`, for now...)

## Installing

- Install [Composer](http://getcomposer.org) and place the executable somewhere in your `$PATH` (for the rest of this README,
I'll reference it as just `composer`)

- Add `filp/whoops` to your project's `composer.json:

```json
{
    "require": {
        "filp/whoops": "1.*"
    }
}
```

- Install/update your dependencies

```bash
$ cd my_project
$ composer install
```

And you're good to go! Have a look at the **example files** in `examples/` to get a feel for how things work.
I promise it's really simple!

## Integrating with Silex

**whoops** comes packaged with a Silex Service Provider: `Whoops\Provider\Silex\WhoopsServiceProvider`. Using it
in your existing Silex project is easy:

```php

require 'vendor/autoload.php';

use Silex\Application;

// ... some awesome code here ...

if($app['debug']) {
    $app->register(new Whoops\Provider\Silex\WhoopsServiceProvider);
}

// ...

$app->run();
```

And that's about it. By default, you'll get the pretty error pages if something goes awry in your development
environment, but you also have full access to the **whoops** library, obviously. For example, adding a new handler
into your app is as simple as extending `whoops`:

```php
$app['whoops'] = $app->extend('whoops', function($whoops) {
    $whoops->pushHandler(new DeleteWholeProjectHandler);
    return $whoops;
});
```

## Integrating with Laravel 3

User [@hdias](https://github.com/hdias) contributed a simple guide/example to help you integrate **whoops** with Laravel 3's IoC container, available at:

https://gist.github.com/hdias/5169713#file-start-php


## Integrating with Laravel 4/Illuminate

User [@schickling](https://github.com/schickling) contributed a service provider for Laravel 4. Just include this in your app/config/app.php in the "providers" array:

```php
'Whoops\Provider\Illuminate\WhoopsServiceProvider'
```

Alternatively, [@dberry37388](https://github.com/dberry37388)'s [laravel4-support](https://github.com/dberry37388/laravel4-support) package also integrates **whoops** into your fancy-schmancy Laravel stack.

## Available Handlers

**whoops** currently ships with the following built-in handlers, available in the `Whoops\Handler` namespace:

- [`PrettyPageHandler`](https://github.com/filp/whoops/blob/master/src/Whoops/Handler/PrettyPageHandler.php) - Shows a pretty error page when something goes pants-up
- [`CallbackHandler`](https://github.com/filp/whoops/blob/master/src/Whoops/Handler/CallbackHandler.php) - Wraps a closure or other callable as a handler. You do not need to use this handler explicitly, **whoops** will automatically wrap any closure or callable you pass to `Whoops\Run::pushHandler`
- [`JsonResponseHandler`](https://github.com/filp/whoops/blob/master/src/Whoops/Handler/JsonResponseHandler.php) - Captures exceptions and returns information on them as a JSON string. Can be used to, for example, play nice with AJAX requests.

## Contributing

If you want to help, great! Here's a couple of steps/guidelines:

- Fork/clone this repo, and update dev dependencies using Composer

```bash
$ git clone git@github.com:filp/whoops.git
$ cd whoops
$ composer install --dev
```

- Create a new branch for your feature or fix

```bash
$ git checkout -b feature/flames-on-the-side
```

- Add your changes & tests for those changes (in `tests/`).
- Remember to stick to the existing code style as best as possible. When in doubt, follow `PSR-2`.
- Send me a pull request!

If you don't want to go through all this, but still found something wrong or missing, please
**open a new issue report** so that I or others may take care of it.

### TODO/tasks (very short & rough list of current goals)
- Delay `prettify` until needed, instead of doing it for every frame at once
- Cleanup API, specially around Frame handling
- Add extension hooks for `PrettyPageHandler`
- Improve test coverage
- **Document the API**
