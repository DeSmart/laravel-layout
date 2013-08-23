<?php namespace DeSmart\Layout;

use Illuminate\Routing\Router;
use Illuminate\Container\Container;
use Illuminate\Routing\Controllers\Controller as LaravelController;
use Illuminate\Support\Contracts\RenderableInterface as Renderable;

class Controller extends LaravelController {

  /**
   * Layout structure
   *
   * @var array
   */
  protected $structure = array();

  /**
   * Application instance
   *
   * @var Illuminate\Container\Container
   */
  protected $container;

  /**
   * Setup the layout used by the controller.
   *
   * @return void
   */
  protected function setupLayout() {

    if (null !== $this->layout) {
      $this->layout = \View::make($this->layout);
    }
  }

  protected function changeLayout($layout) {
    $this->layout = \View::make($layout);
  }

  public function execute(array $args = null) {
    $self = $this;

    if(null === $args) {
      $args = $this->container['router']->getCurrentRoute()->getParametersWithoutDefaults();
    }

    $mapper = function($callbackString) use ($args, $self) {
      return $self->callCallback($callbackString, $args);
    };

    foreach($this->structure as $block => $callback_list) {
      $mapped = array_map($mapper, $callback_list);
      $this->layout[$block] = join("\n", $mapped);
    }

    return $this->layout;
  }

  public final function callCallback($callbackString, array $args = null) {
    $profiler = isset($this->container['profiler']) ? $this->container['profiler'] : null;

    if(null !== $profiler) {
      $profiler->startTimer($callbackString);
    }

    $output = $this->container['layout']->dispatch($callbackString, $args);

    if(true === $output instanceof Renderable) {
      $output = $output->render();
    }

    if(null !== $profiler) {
      $profiler->endTimer($callbackString);
    }

    return $output;
  }

  public function callAction(Container $app, Router $router, $method, $parameters) {
    // dirty way to assign application instance to controller
    $this->setContainer($app);

    return parent::callAction($app, $router, $method, $parameters);
  }

  /**
   * Set container instance
   *
   * @param \Illuminate\Container\Container $container
   */
  public function setContainer(Container $container) {
    $this->container = $container;
  }

}
