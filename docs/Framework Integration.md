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


# Integrating with Zend Framework 2

User [@zsilbi](https://github.com/zsilbi) contributed a provider for ZF2 integration,
available in the following location:

https://github.com/filp/whoops/tree/master/src/Whoops/Provider/Zend

**Instructions:**

- Add Whoops as a module to you app (/vendor/Whoops)
- Whoops must be the first module:

```php
'modules' => array(
        'Whoops',
        'Application'
   )
```

- Move Module.php from /Whoops/Provider/Zend/Module.php to /Whoops/Module.php
- Use optional configurations in your controller config:

```php
return array(
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions' => true,
        'json_exceptions' => array(
            'display' => true,
            'ajax_only' => true,
            'show_trace' => true
        )
    ),
);
```

- NOTE: ob_clean(); is used to remove previous output, so you may use ob_start(); at the beginning of your app (index.php)


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
        "version": "1.0.0",
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
