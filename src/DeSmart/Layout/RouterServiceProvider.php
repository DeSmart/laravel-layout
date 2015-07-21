<?php namespace DeSmart\Layout;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider;
use Illuminate\Routing\ControllerDispatcher;
use Illuminate\Routing\Router;

class RouterServiceProvider extends RouteServiceProvider
{

    public function boot(Router $router)
    {
/*        $this->app->bind(ControllerDispatcher::class, function() {
            return new \DeSmart\Layout\ControllerDispatcher($this->app['router'], $this->app);
        });*/

/*        $this->app->resolving(Controller::class, function ($object) {
            $object->setLayoutDispatcher($this->app['layout']);
            $object->setViewFactory($this->app['view']);
            $object->setRouter($this->app['router']);
        });*/
    }
}
