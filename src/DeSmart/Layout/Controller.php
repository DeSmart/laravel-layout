<?php namespace DeSmart\Layout;

use Symfony\Component\HttpFoundation\Response;
use Illuminate\Routing\Controller as LaravelController;
use Illuminate\Contracts\Support\Renderable;

class Controller extends LaravelController {

  /**
   * Layout structure
   *
   * @var array
   */
  protected $structure = array();

  /**
   * @var \Illuminate\View\Environment
   */
  protected $viewFactory;

  /**
   * @var \DeSmart\Layout\Layout
   */
  protected $layoutDispatcher;

  /**
   * Array of view data
   *
   * @var array
   */
  protected $data = array();

  protected $layout;

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
    $this->layout = new LazyView($layout);
  }

  public function execute(array $args = null) {
    $this->setupLayout();

    $this->layout->setEnvironment($this->viewFactory);

    if(null === $args) {
      $args = static::$router->getCurrentRoute()->parametersWithoutNulls();
    }

    foreach($this->structure as $block => $callback_list) {
      $blocks = array();

      foreach($callback_list as $callback) {
        $response = $this->handleResponse($this->callCallback($callback, $args));

        if(true === $response instanceof Response) {
          return $response;
        }

        $blocks[] = $response;
      }

      $this->layout->with($block, join("\n", $blocks));
    }

    $this->layout->with($this->data);

    return $this->layout;
  }

  protected function handleResponse($response) {

    if(true === $response instanceof Renderable) {
      $response = $response->render();
    }

    return $response;
  }

  public final function callCallback($callbackString, array $args = null) {
    if ( ! $this->layoutDispatcher) {
      $this->layoutDispatcher = new Layout();
    }

    $output = $this->layoutDispatcher->dispatch($callbackString, $args);

    if(true === $output instanceof Renderable) {
      $output = $output->render();
    }

    return $output;
  }

  /**
   * @param \DeSmart\Layout\Layout $dispatcher
   */
  public function setLayoutDispatcher(Layout $dispatcher) {
    $this->layoutDispatcher = $dispatcher;
  }

  /**
   * @param \Illuminate\View\Factory $factory
   */
  public function setViewFactory(\Illuminate\View\Factory $factory) {
    $this->viewFactory = $factory;
  }

}
