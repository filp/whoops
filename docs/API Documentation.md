# API Documentation

### Core Classes:
- [`Whoops\Run`](#whoops-run) - The main `Whoops` class - represents the stack and current execution
- [`Whoops\Handler\Handler` and `Whoops\Handler\HandlerInterface`](#handler-abstract) - Abstract representation of a Handler, and utility methods
- [`Whoops\Exception\Inspector`](#inspector) - Exposes methods to inspect an exception
- [`Whoops\Exception\FrameCollection`](#frame-collection) - Exposes methods to work with a list of frames
- [`Whoops\Exception\Frame`](#frame) - Exposes methods to inspect a single stack trace frame from an exception

### Core Handlers:
- [`Whoops\Handler\CallbackHandler`](#handler-callback) - Wraps regular closures as handlers
- [`Whoops\Handler\JsonResponseHandler`](#handler-json) - Formats errors and exceptions as a JSON payload
- [`Whoops\Handler\PrettyPageHandler`](#handler-pretty) - Outputs a detailed, fancy error page

### Core Functions:
- [`Whoops\Util\Misc::isAjaxRequest()`](#fn-ajax) - Determines whether the current request was triggered by XMLHttpRequest
- [`Whoops\Util\Misc::isCommandLine()`](#fn-cli) - Determines whether the current request was triggered via php commandline interface (CLI)


# Core Classes:

## <a name="whoops-run"></a> `Whoops\Run`

The `Run` class models an instance of an execution, and integrates the methods to control whoops' execution in that context, and control the handlers stack.

### Constants

```php
string Run::EXCEPTION_HANDLER // (name for exception handler method)
string Run::ERROR_HANDLER     // (name for error handler method)
string Run::SHUTDOWN_HANDLER  // (name for shutdown handler method)
```

### Methods

```php
Run::prependHandler(Whoops\HandlerInterface $handler)
  #=> Whoops\Run
Run::appendHandler(Whoops\HandlerInterface $handler)
  #=> Whoops\Run
Run::removeFirstHandler()
  #=> null
Run::removeLastHandler()
  #=> null

// Returns all handlers in the stack
Run::getHandlers()
 #=> Whoops\HandlerInterface[]

// Returns a Whoops\Inspector instance for a given Exception
Run::getInspector(Exception $exception)
 #=> Whoops\Exception\Inspector

// Registers this Whoops\Run instance as an error/exception/shutdown
// handler with PHP
Run::register()
 #=> Whoops\Run

// I'll let you guess this one
Run::unregister()
 #=> Whoops\Run

// Send a custom exit code in CLI context (default: 1)
Run::sendExitCode($code = null)
 #=> int

// If true, allows Whoops to terminate script execution (default: true)
Run::allowQuit($allowQuit = null)
 #=> bool

// Silence errors for paths matching regular expressions and PHP error constants.
// Can be called multiple times.
Run::silenceErrorsInPaths($patterns, $levels = E_STRICT | E_DEPRECATED)
 #=> Whoops\Run
 
// If true, allows Whoops to send output produced by handlers directly
// to the client. You'll want to set this to false if you want to
// package the handlers' response into your HTTP response abstraction
// or something (default: true)
Run::writeToOutput($send = null)
 #=> bool

// ** HANDLERS **
// These are semi-internal methods that receive input from
// PHP directly. If you know what you're doing, you can
// also call them directly

// Handles an exception with the current stack. Returns the
// output produced by handlers.
Run::handleException(Exception $exception)
 #=> string

// Handles an error with the current stack. Errors are
// converted into SPL ErrorException instances
Run::handleError(int $level, string $message, string $file = null, int $line = null)
 #=> null

// Hooked as a shutdown handler, captures fatal errors and handles them
// through the current stack:
Run::handleShutdown()
 #=> null

// adds a new frame filter callback to the frame filters stack
Run::addFrameFilter()
 #=> Whoops\Run
```

## <a name="handler-abstract"></a> `Whoops\Handler\Handler` & `Whoops\Handler\HandlerInterface`

This abstract class contains the base methods for concrete handler implementations. Custom handlers can extend it, or implement the `Whoops\Handler\HandlerInterface` interface.

### Constants
```php
int Handler::DONE          // If returned from HandlerInterface::handle, does absolutely nothing.
int Handler::LAST_HANDLER  // ...tells whoops to not execute any more handlers after this one.
int Handler::QUIT          // ...tells whoops to quit script execution immediately.
```

### Methods

```php
// Custom handlers should expose this method, which will be called once an
// exception needs to be handled. The Handler::* constants can be used to
// signal the underlying logic as to what to do next.
HandlerInterface::handle()
 #=> null | int

// Sets the Run instance for this handler
HandlerInterface::setRun(Whoops\Run $run)
 #=> null

// Sets the Inspector instance for this handler
HandlerInterface::setInspector(Whoops\Exception\Inspector $inspector)
 #=> null

// Sets the Exception for this handler to handle
HandlerInterface::setException(Exception $exception)
 #=> null
```

## <a name="inspector"></a> `Whoops\Exception\Inspector`

The `Inspector` class provides methods to inspect an exception instance, with particular focus on its frames/stack-trace.

### Methods

```php
Inspector::__construct(Exception $exception)
 #=> null

// Returns the Exception instance being inspected
Inspector::getException()
 #=> Exception

// Returns the string name of the Exception being inspected
// A faster way of doing get_class($inspector->getException())
Inspector::getExceptionName()
 #=> string

// Returns the string message for the Exception being inspected
// A faster way of doing $inspector->getException()->getMessage()
Inspector::getExceptionMessage()
 #=> string

// Returns an iterator instance for all the frames in the stack
// trace for the Exception being inspected.
Inspector::getFrames()
 #=> Whoops\Exception\FrameIterator
```

## <a name="frame-collection"></a> `Whoops\Exception\FrameCollection`

The `FrameCollection` class exposes a fluent interface to manipulate and examine a
collection of `Frame` instances.

`FrameCollection` objects are **serializable**.

### Methods

```php
// Returns the number of frames in the collection
// May also be called as count($frameCollection)
FrameCollection::count()
 #=> int

// Filter the Frames in the collection with a callable.
// The callable must accept a Frame object, and return
// true to keep it in the collection, or false not to.
FrameCollection::filter(callable $callable)
 #=> FrameCollection

// See: array_map
// The callable must accept a Frame object, and return
// a Frame object, doesn't matter if it's the same or not
// - will throw an UnexpectedValueException if something
// else is returned.
FrameCollection::map(callable $callable)
 #=> FrameCollection
```

## <a name="frame"></a> `Whoops\Exception\Frame`

The `Frame` class models a single frame in an exception's stack trace. You can use it to retrieve info about things such as frame context, file, line number. Additionally, you have available functionality to add comments to a frame, which is made available to other handlers.

`Frame` objects are **serializable**.

### Methods

```php
// Returns the file path for the file where this frame occurred.
// The optional $shortened argument allows you to retrieve a
// shorter, human-readable file path for display.
Frame::getFile(bool $shortened = false)
 #=> string | null (Some frames do not have a file path)

// Returns the line number for this frame
Frame::getLine()
 #=> int | null

// Returns the class name for this frame, if it occurred
// within a class/instance.
Frame::getClass()
 #=> string | null

// Returns the function name for this frame, if it occurred
// within a function/method
Frame::getFunction()
 #=> string | null

// Returns an array of arguments for this frame. Empty if no
// arguments were provided.
Frame::getArgs()
 #=> array

// Returns the full file contents for the file where this frame
// occurred.
Frame::getFileContents()
 #=> string | null

// Returns an array of lines for a file, optionally scoped to a
// given range of line numbers. i.e: Frame::getFileLines(0, 3)
// returns the first 3 lines after line 0 (1)
Frame::getFileLines(int $start = 0, int $length = null)
 #=> array | null

// Adds a comment to this Frame instance. Comments are shared
// with everything that can access the frame instance, obviously,
// so they can be used for a variety of inter-operability purposes.
// The context option can be used to improve comment filtering.
// Additionally, if frames contain URIs, the PrettyPageHandler
// will automagically convert them to clickable anchor elements.
Frame::addComment(string $comment, string $context = 'global')
 #=> null

// Returns all comments for this instance optionally filtered by
// a string context identifier.
Frame::getComments(string $filter = null)
 #=> array
```


# Core Handlers

## <a name="handler-callback"></a> `Whoops\Handler\CallbackHandler`

The `CallbackHandler` handler wraps regular PHP closures as valid handlers. Useful for quick prototypes or simple handlers. When you pass a closure to `Run::pushHandler`, it's automatically converted to a `CallbackHandler` instance.

```php
<?php

use Whoops\Handler\Handler;

$run->pushHandler(function($exception, $inspector, $run) {
    var_dump($exception->getMessage());
    return Handler::DONE;
});

$run->popHandler() // #=> Whoops\Handler\CallbackHandler
```

### Methods

```php
// Accepts any valid callable
// For example, a closure, a string function name, an array
// in the format array($class, $method)
CallbackHandler::__construct($callable)
 #=> null

CallbackHandler::handle()
 #=> int | null
```

## <a name="handler-json"></a> `Whoops\Handler\JsonResponseHandler`

The `JsonResponseHandler`, upon receiving an exception to handle, simply constructs a `JSON` payload, and outputs it. Methods are available to control the detail of the output, and if it should only execute for AJAX requests - paired with another handler under it, such as the `PrettyPageHandler`, it allows you to have meaningful output for both regular and AJAX requests. Neat!

The `JSON` body has the following format:

```json
{
  "error": {
    "type": "RuntimeException",
    "message": "Something broke!",
    "file": "/var/project/foo/bar.php",
    "line": 22,

     # if JsonResponseHandler::addTraceToOutput(true):
     "trace": [
        { "file": "/var/project/foo/index.php",
          "line": 157,
          "function": "handleStuffs",
          "class": "MyApplication\DoerOfThings",
          "args": [ true, 10, "yay method arguments" ] },
        # ... more frames here ...
     ]
  }
}
```

### Methods

```php

// Should detailed stack trace output also be added to the
// JSON payload body?
JsonResponseHandler::addTraceToOutput(bool $yes = null)
 #=> bool

JsonResponseHandler::handle()
 #=> int | null
```

## <a name="handler-pretty"></a> `Whoops\Handler\PrettyPageHandler`

The `PrettyPageHandler` generates a fancy, detailed error page which includes code views for all frames in the stack trace, environment details, etc. Super neat. It produces a bundled response string that does not require any further HTTP requests, so it's fit to work on pretty much any environment and framework that speaks back to a browser, without you having to explicitly hook it up to your framework/project's routing mechanisms.

### Methods

```php
// Adds a key=>value table of arbitrary data, labeled by $label, to
// the output. Useful where you want to display contextual data along
// with the error, about your application or project.
PrettyPageHandler::addDataTable(string $label, array $data)
 #=> null

// Similar to PrettyPageHandler::addDataTable, but accepts a callable
// that will be called only when rendering an exception. This allows
// you to gather additional data that may not be available very early
// in the process.
PrettyPageHandler::addDataTableCallback(string $label, callable $callback)
 #=> null

// Returns all data tables registered with this handler. Optionally
// accepts a string label, and will only return the data under that
// label.
PrettyPageHandler::getDataTables(string $label = null)
 #=> array | array[]

// Sets the title for the error page
PrettyPageHandler::setPageTitle(string $title)
 #=> null

// Returns the title for the error page
PrettyPageHandler::getPageTitle()
 #=> string

// Returns a list of string paths where resources
// used by this handler are searched for - the template and CSS
// files.
PrettyPageHandler::getResourcesPaths()
 #=> array

// Adds a string path to the location of resources for the
// handler. Useful if you want to roll your own template
// file (pretty-template.php and pretty-page.css) while
// still using the logic this handler provides
PrettyPageHandler::addResourcePath(string $resourcesPath)
 #=> null

// Sets an editor to use to open referenced files, either by
// a string identifier, or as an arbitrary callable that returns
// a string or an array that can be used as an href attribute.
// Available built-in editors can be found here: https://github.com/filp/whoops/blob/master/docs/Open Files In An Editor.md

PrettyPageHandler::setEditor(string $editor)
PrettyPageHandler::setEditor(function ($file, $line) { return string })

// Additionally you may want that the link acts as an ajax request (e.g. Intellij platform)
PrettyPageHandler::setEditor(function ($file, $line) {
        return array(
            'url' => "http://localhost:63342/api/file/?file=$file&line=$line",
            'ajax' => true
        );
    }
)
 #=> null

// Similar to PrettyPageHandler::setEditor, but allows you
// to name your custom editor, thus sharing it with the
// rest of the application. Useful if, for example, you integrate
// Whoops into your framework or library, and want to share
// support for extra editors with the end-user.
//
// $resolver may be a callable, like with ::setEditor, or a string
// with placeholders %file and %line.
// For example:
// $handler->addEditor('whatevs', 'whatevs://open?file=file://%file&line=%line')
PrettyPageHandler::addEditor(string $editor, $resolver)
 #=> null



PrettyPageHandler::handle()
 #=> int | null
```


# Core Functions:

## <a name="fn-ajax"></a> `Whoops\Util\Misc::isAjaxRequest()`
 #=> boolean

```php
// Use a certain handler only in AJAX triggered requests
if (Whoops\Util\Misc::isAjaxRequest()){
  $run->addHandler($myHandler);
}
```

## <a name="fn-cli"></a> `Whoops\Util\Misc::isCommandLine()`
 #=> boolean

```php
// Use a certain handler only in php cli
if (Whoops\Util\Misc::isCommandLine()){
  $run->addHandler($myHandler);
}
```

```php
/*
Output the error message only if using command line.
else, output to logger if available.
Allow to safely add this handler to web pages.
*/
$plainTextHandler = new PlainTextHandler();
if (!Whoops\isCommandLine()){
  $plainTextHandler->loggerOnly(true);
}
$run->addHandler($myHandler);
```
