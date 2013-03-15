<?php namespace Damnit\Illuminate;

use Illuminate\Support\ServiceProvider;

use Damnit\Handler\PrettyPageHandler;
use Damnit\Run;

/**
 * Provides support for Illuminate (Laravel 4)
 */
class DamnitServiceProvider extends ServiceProvider {

	/**
	 * Register the service provider for damnit to laravel
	 *
	 * @return void
	 */
	public function register()
	{
		$app = $this->app;

		$this->app['damnit.handler'] = $this->app->share(function($app)
		{
			return new PrettyPageHandler;
		});

		$this->app['damnit'] = $this->app->share(function($app)
		{
			$run = new Run;

			return $run->pushHandler($app['damnit.handler']);
		});

		$this->app->error(function($e) use ($app)
		{
			$app['damnit']->handleException($e);
		});
	}

}