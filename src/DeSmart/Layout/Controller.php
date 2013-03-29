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

  public function dispatch() {

    foreach($this->structure as $block => $callback_list) {
      $mapped = array_map(array($this, 'callCallback'), $callback_list);
      $this->layout[$block] = join("\n", $mapped);
    }

    return $this->layout;
  }

  private function callCallback($callbackString) {
    $callback = $this->makeCallback($callbackString);
    $profiler = isset($this->container['profiler']) ? $this->container['profiler'] : null;

    if(null !== $profiler) {
      $profiler->startTimer($callbackString);
    }

    $output = call_user_func($callback);

    if(null !== $profiler) {
      $profiler->endTimer($callbackString);
    }

    if(true === $output instanceof Renderable) {
      return $output->render();
    }

    return $output;
  }

  public function callAction(Container $container, Router $router, $method, $parameters) {
    $this->container = $container;

    return parent::callAction($container, $router, $method, $parameters);
  }

  private function makeCallback($callbackString) {
    list($class, $method) = explode('@', $callbackString);

    return array($this->container->make($class), $method);
  }
}
