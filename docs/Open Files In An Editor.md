# Open Files In An Editor

When using the pretty error page feature, whoops comes with the ability to
open referenced files directly in your IDE or editor.
This feature only works in case your php-source files are locally accessible to the machine on which the editor is installed.

```php
<?php

use Whoops\Handler\PrettyPageHandler;

$handler = new PrettyPageHandler;
$handler->setEditor('sublime');
```

The following editors are currently supported by default.

- `emacs`    - Emacs
- `idea`     - IDEA
- `macvim`   - MacVim
- `phpstorm` - PhpStorm (on Linux you might need to manually install a [handler](https://github.com/sanduhrs/phpstorm-url-handler))
- `sublime`  - Sublime Text 2 and possibly 3 (on OS X you might need [a special handler](https://github.com/inopinatus/sublime_url))
- `textmate` - Textmate
- `xdebug`   - xdebug (uses [xdebug.file_link_format](http://xdebug.org/docs/all_settings#file_link_format))
- `vscode`   - VSCode (ref [Opening VS Code with URLs](https://code.visualstudio.com/docs/editor/command-line#_opening-vs-code-with-urls))
- `atom`     - Atom (ref [Add core URI handlers](https://github.com/atom/atom/pull/15935))
- `espresso` - Espresso
- `netbeans` - Netbeans (ref [xdebug.file_link_format](http://xdebug.org/docs/all_settings#file_link_format))

Adding your own editor is simple:

```php

$handler->setEditor(function($file, $line) {
    return "whatever://open?file=$file&line=$line";
});

```

If PHP runs somewhere else (Docker, a VM, a remote server), map server paths to
your local checkout with the `pathMappings` option. It works with built-in and
custom editors; use absolute local paths, since editors do not expand `~`.
Prefixes match whole directories, and the longest matching prefix wins.

```php

$handler->setEditor('vscode', [
    'pathMappings' => [
        '/var/www/app' => '/Users/me/code/app',
    ],
]);

```

You can add [IntelliJ Platform](https://github.com/pinepain/PhpStormOpener#phpstormopener) support like this:
```php

$handler->setEditor(
    function ($file, $line) {
        // IntelliJ platform requires that you send an Ajax request, else the browser will quit the page
        return array(
            'url' => "http://localhost:63342/api/file/?file=$file&line=$line",
            'ajax' => true
        );
    }
);

```
