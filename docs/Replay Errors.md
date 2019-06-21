# Replay Errors

You can replay Errors that happened in production if you serialize them and store them to later replay in a development Whoops:

```php
$serialized_exception = serialize(new Exception('Something has happened!'));

$whoops = new \Whoops\Run;
$whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
$whoops->register();
$whoops->handleException(unserialize($serialized_exception));
```

https://github.com/filp/whoops/issues/623
