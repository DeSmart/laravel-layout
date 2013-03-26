<?php namespace DeSmart\Layout;

use Controller as BaseController;
use Illuminate\Foundation\Application as Container;
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

  public function __construct(Container $container) {
    $this->container = $container;
  }

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
    $output = call_user_func($callback);

    if(true === $output instanceof Renderable) {
      return $output->render();
    }

    return $output;
  }

  private function makeCallback($callbackString) {
    list($class, $method) = explode('@', $callbackString);

    return array($this->container->make($class), $method);
  }
}
