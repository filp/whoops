# Damnit
php errors for cool kids

[![Build Status](https://travis-ci.org/filp/damnit.png?branch=master)](https://travis-ci.org/filp/damnit)

-----

![Damnit!](http://i.imgur.com/sBUMl5s.png)

`Damnit` is an error handler base/framework for PHP. Out-of-the-box, it provides a pretty
error interface that helps you debug your web projects, but at heart it's a simple yet
powerful stacked error handling system.

This library is currently in a **heavy development phase, and not yet ready for consumption.**

## Installing

- Install [Composer](http://getcomposer.org) and place the executable somewhere in your `$PATH` (for the rest of this README,
I'll reference it as just `composer`)

- Add `filp/damnit` to your project's `composer.json:

```json
{
    "require": {
        "filp/damnit": "dev-master"
    }
}
```

- Install/update your dependencies

```bash
$ cd my_project
$ composer install
```

And you're good to go! Have a look at the **example files**: `example.php` and `example-silex.php` to get a look at
how things work.

## Integrating with Silex

`Damnit` comes packaged with a Silex Service Provider: `Damnit\Silex\DamnitServiceProvider`. Using it
in your existing Silex project is easy:

```php

require 'vendor/autoload.php';

use Silex\Application;

// ... some awesome code here ...

if($app['debug']) {
    $app->register(new Damnit\Silex\DamnitServiceProvider);
}

// ...

$app->run();
```

And that's about it. By default, you'll get the pretty error pages if something goes awry in your development
environment, but you also have full access to the `Damnit` library, obviously. For example, adding a new handler
into your app is as simple as extending `damnit`:

```php
$app['damnit'] = $app->extend('damnit', function($damnit) {
    $damnit->pushHandler(new DeleteWholeProjectHandler);
    return $damnit;
});
```

## Contributing

If you want to help, great! Here's a couple of steps/guidelines:

- Fork/clone this repo, and update dev dependencies using Composer

```bash
$ git clone git@github.com:filp/damnit.git
$ cd damnit
$ composer install --dev
```

- Create a new branch for your feature or fix

```bash
$ git checkout -b feature/flames-on-the-side
```

- Add your changes & tests for those changes (in `tests/`).
- Remember to stick to the existing code style as best as possible. When in doubt, follow `PSR-2`.
- Send me a pull request!

### TODO/tasks (very short & rough list of current goals)
- Improve code view
- Improve UI in situations where there's a very long frame list
- Delay `prettify` until needed, instead of doing it for every frame at once
- Cleanup API, specially around Frame handling
- Add extension hooks for `PrettyPageHandler`
- Improve test coverage
