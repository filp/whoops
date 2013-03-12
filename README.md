# Damnit
php errors for cool kids

[![Build Status](https://travis-ci.org/filp/damnit.png?branch=master)](https://travis-ci.org/filp/damnit)

-----

`Damnit` is an error handler base/framework for PHP. Out-of-the-box, it provides a pretty
error interface that helps you debug your web projects, but at heart it's a simple yet
powerful stacked error handling system.

## Basic Usage:

To use `Damnit` with default settings, for a web project, simply create an instance and
`register()` it:

```php

$damnit = new Damnit\Run;
$damnit->register();

```
