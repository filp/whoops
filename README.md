# damnit 
php errors for cool kids

[![Build Status](https://travis-ci.org/filp/damnit.png)](https://travis-ci.org/filp/damnit)

-----

`damnit` is an error handler base/framework for PHP. Out-of-the-box, it provides a pretty
error interface that helps you debug your web projects, but at heart it's a simple yet
powerful stacked error system.

## Basic Usage:

To use `damnit` with default settings, for a web project, simply create an instance and
`register()` it:

```php

$damnit = new DamnIt\Run;
$damnit->register();

```