# Open Files In An Editor

When using the pretty error page feature, whoops comes with the ability to
open referenced files directly in your IDE or editor.

```php
<?php

use Whoops\Handler\PrettyPageHandler;

$handler = new PrettyPageHandler;
$handler->setEditor('sublime');
```

The following editors are currently supported by default.

- `sublime`  - Sublime Text 2
- `emacs`    - Emacs
- `textmate` - Textmate
- `macvim`   - MacVim
- `xdebug`   - xdebug (uses [xdebug.file_link_format](http://xdebug.org/docs/all_settings#file_link_format))
- `phpstorm` - PhpStorm

Adding your own editor is simple:

```php

$handler->setEditor(function($file, $line) {
    return "whatever://open?file=$file&line=$line";
});

```

You can add [IntelliJ Platform](https://github.com/pinepain/PhpStormOpener#phpstormopener) support like this:
```php

$handler->setEditor(
    function ($file, $line) {
        // if your development server is not local it's good to map remote files to local
        $translations = array('^' . __DIR__ => '~/Development/PhpStormOpener'); // change to your path

        foreach ($translations as $from => $to) {
            $file = preg_replace('#' . $from . '#', $to, $file, 1);
        }

        // Intellig platform requires that you send an Ajax request, else the browser will quit the page
        return array(
            'url' => "http://localhost:63342/api/file/?file=$file&line=$line",
            'ajax' => true
        );
    }
);

```
