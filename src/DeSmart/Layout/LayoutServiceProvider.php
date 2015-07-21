<?php namespace DeSmart\Layout;

use Illuminate\Support\ServiceProvider;

class LayoutServiceProvider extends ServiceProvider
{

    public function register()
    {
        $this->registerLayout();
    }

    protected function registerLayout()
    {
        $this->app['layout'] = $this->app->share(function ($app) {
            return new Layout($app);
        });
    }

    public function provides()
    {
        return array('layout');
    }

}
