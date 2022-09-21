# 2.14.5

* Allow ArrayAccess on super globals

# 2.14.4

* Fix PHP 5.5 support.
* Allow to use psr/log 2 or 3.

# 2.14.3

* Support PHP 8.1

# 2.14.1

* Fix syntax highlighting scrolling too far.
* Improve the way we detect xdebug linkformat.

# 2.14.0

* Switched syntax highlighting to Prism.js

Avoids licensing issues with prettify, and uses a maintained, modern project.

# 2.13.0

* Add Netbeans editor

# 2.12.1

* Avoid redirecting away from an error.

# 2.12.0

* Hide non-string values in super globals when requested.

# 2.11.0

* Customize exit code

# 2.10.0

* Better chaining on handler classes

# 2.9.2

* Fix copy button styles

# 2.9.1

* Fix xdebug function crash on PHP 8

# 2.9.0

* JsonResponseHandler includes the exception code

# 2.8.0

* Support PHP 8

# 2.7.3

* PrettyPageHandler functionality to hide superglobal keys has a clearer name hideSuperglobalKey

# 2.7.2

* PrettyPageHandler now accepts custom js files
* PrettyPageHandler templateHelper is now accessible through inheritance

# 2.7.1

* Fix a PHP warning in some cases with anonymous classes.

# 2.7.0

* removeFirstHandler and removeLastHandler.

# 2.6.0

* Fix 2.4.0 pushHandler changing the order of handlers.

# 2.5.1

* Fix error messaging in a rare case.

# 2.5.0

* Automatically configure xdebug if available.

# 2.4.1

* Try harder to close all output buffers

# 2.4.0

* Allow to prepend and append handlers.

# 2.3.2

* Various fixes from the community.

# 2.3.1

* Prevent exception in Whoops when caught exception frame is not related to real file

# 2.3.0

* Show previous exception messages.

# 2.2.0

* Support PHP 7.2

# 2.1.0

* Add a `SystemFacade` to allow clients to override Whoops behavior.
* Show frame arguments in `PrettyPageHandler`.
* Highlight the line with the error.
* Add icons to search on Google and Stack Overflow.

# 2.0.0

Backwards compatibility breaking changes:

* `Run` class is now `final`. If you inherited from `Run`, please now instead use a custom `SystemFacade` injected into the `Run` constructor, or contribute your changes to our core.
* PHP < 5.5 support dropped.
