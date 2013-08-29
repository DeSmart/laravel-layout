<?php namespace DeSmart\Layout;

use Illuminate\Routing\Router;
use Illuminate\Container\Container;
use Symfony\Component\HttpFoundation\RedirectResponse;
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
      $this->changeLayout($this->layout);
    }
  }

  protected function changeLayout($layout) {
    $this->layout = $this->container['view']->make($layout);
  }

  public function execute(array $args = null) {

    if(null === $args) {
      $args = $this->container['router']->getCurrentRoute()->getParametersWithoutDefaults();
    }

    foreach($this->structure as $block => $callback_list) {
      $blocks = array();

      foreach($callback_list as $callback) {
        $response = $this->handleResponse($this->callCallback($callback, $args));

        if(true === $response instanceof RedirectResponse) {
          return $response;
        }

        $blocks[] = $response;
      }

      $this->layout[$block] = join("\n", $blocks);
    }

    return $this->layout;
  }

  protected function handleResponse($response) {

    if(true === $response instanceof Renderable) {
      $response = $response->render();
    }

    return $response;
  }

  /**
   * Call given callback
   *
   * @see \DeSmart\Layout\Layout::dispatch()
   * @param string $string
   * @param array  $args
   * @return mixed
   */
  protected final function callCallback($string, array $args = null) {
    $profiler = isset($this->container['profiler']) ? $this->container['profiler'] : null;

    if(null !== $profiler) {
      $profiler->startTimer($string);
    }

    $response = $this->container['layout']->dispatch($string, $args);

    if(null !== $profiler) {
      $profiler->endTimer($string);
    }

    return $response;
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
