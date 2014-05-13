# Integrating with Silex

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


# Integrating with Phalcon

**whoops** comes packaged with a Phalcon Service Provider: `Whoops\Provider\Phalcon\WhoopsServiceProvider`. Using it
in your existing Phalcon project is easy. The provider uses the default Phalcon DI unless you pass a DI instance into the constructor.

```php
new Whoops\Provider\Phalcon\WhoopsServiceProvider;

// --- or ---

$di = Phalcon\DI\FactoryDefault;
new Whoops\Provider\Phalcon\WhoopsServiceProvider($di);
```


# Contributing an integration with a framework

Lately we're prefering to keep integration libraries out of the Whoops core.
If possible, consider managing an official Whoops-SomeFramework integration.

The procedure is not hard at all.

1. Keep your integration classes and instructions in a repository of your own;
2. Create a `composer.json` file in your repository with contents similar to the following:

    ```
    {
        "name": "username/whoops-someframework",
        "description": "Integrates the Whoops library into SomeFramework",
        "require": {
            "filp/whoops": "1.*"
        }
    }
    ```

3. [Register it with Packagist](https://packagist.org/packages/submit).

Once that is done, please create an issue and we will add a link to it in our README.

SomeFramework users then would write this in their `composer.json`:

    "require": {
        "username/whoops-someframework": "*"
    }

This would also install Whoops and you'd be able to release updates to your package as quickly as you wish them to.
