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
