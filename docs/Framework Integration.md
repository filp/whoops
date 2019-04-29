# Contributing an integration with a framework

Lately we're preferring to keep integration libraries out of the Whoops core.
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

```
    "require": {
        "username/whoops-someframework": "*"
    }
```

This would also install Whoops and you'd be able to release updates to your package as quickly as you wish them to.
