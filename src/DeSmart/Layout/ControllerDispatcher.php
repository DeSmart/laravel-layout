<?php namespace DeSmart\Layout;

use Illuminate\Routing\ControllerDispatcher as Dispatcher;

class ControllerDispatcher extends Dispatcher
{

    /**
     * {@inheritdoc}
     */
    protected function makeController($controller)
    {
        $controller = parent::makeController($controller);

        if (true === $controller instanceof Controller) {
            $controller->setLayoutDispatcher($this->container['layout']);
            $controller->setViewFactory($this->container['view']);
            $controller->setRouter($this->container['router']);
        }

        return $controller;
    }

}
