<?php namespace DeSmart\Layout;

use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;

class LayoutServiceProvider extends ServiceProvider {

  public function register() {
    $this->registerLayout();
    $this->registerControllerDispatcher();
  }

  protected function registerLayout() {
    $this->app['layout'] = $this->app->share(function($app) {
      return new Layout($app);
    });
  }

  protected function registerControllerDispatcher() {
    $this->app->extend('router', function(Router $router, $container) {
      $router->setControllerDispatcher(new ControllerDispatcher($router, $container));

      return $router;
    });
  }

  public function provides() {
    return array('layout');
  }

}
