<?php namespace DeSmart\Layout;

use Controller as BaseController;
use Illuminate\Container\Container;
use Illuminate\Support\Contracts\RenderableInterface as Renderable;
use Illuminate\Routing\Router;

class Controller extends BaseController {

  /**
   * @var Container
   */
  protected $container;

  /**
   * Layout structure
   *
   * @var array
   */
  protected $structure = array();

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

  public function execute(array $args = null) {
    $self = $this;

    if(null === $args) {
      $args = $this->app['router']->getCurrentRoute()->getParametersWithoutDefaults();
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
    $profiler = isset($this->app['profiler']) ? $this->app['profiler'] : null;

    if(null !== $profiler) {
      $profiler->startTimer($callbackString);
    }

    $output = $this->app['layout']->dispatch($callbackString, $args);

    if(true === $output instanceof Renderable) {
      $output = $output->render();
    }

    if(null !== $profiler) {
      $profiler->endTimer($callbackString);
    }

    return $output;
  }

  public function callAction(Container $app, Router $router, $method, $parameters) {
    $this->app = $app;

    return parent::callAction($app, $router, $method, $parameters);
  }
}
