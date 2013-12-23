# whoops 
php errors for cool kids

As a Lithium Library for Lithium framework (https://github.com/UnionOfRAD/lithium)


[![Build Status](https://travis-ci.org/filp/whoops.png?branch=master)](https://travis-ci.org/filp/whoops) [![Total Downloads](https://poser.pugx.org/filp/whoops/downloads.png)](https://packagist.org/packages/filp/whoops)  [![Latest Stable Version](https://poser.pugx.org/filp/whoops/v/stable.png)](https://packagist.org/packages/filp/whoops)


-----

![Whoops!](http://i.imgur.com/xiZ1tUU.png)

**whoops** is an error handler base/framework for PHP. Out-of-the-box, it provides a pretty
error interface that helps you debug your web projects, but at heart it's a simple yet
powerful stacked error handling system.

## Usage

### Integrated with Lithium (http://lithify.me/)

**whoops** packaged as a lithium library.
Simply add in config/bootstrap/libraries.php


```php

//at the end of the file
Libraries::add('whoops');

```

And that's about it. 
Try throwing a fake exception in a controller 


```php

class AlertController extends \lithium\action\Controller
{
	public function index()
	{
		throw new Exception("Something broke!");
	}

}
```

## Authors

This library was primarily developed by [Filipe Dobreira](https://github.com/filp).

A lot of awesome fixes and enhancements were also sent in by contributors, which you can find **[in this page right here](https://github.com/filp/whoops/contributors)**.


[![Bitdeli Badge](https://d2weczhvl823v0.cloudfront.net/filp/whoops/trend.png)](https://bitdeli.com/free "Bitdeli Badge")

