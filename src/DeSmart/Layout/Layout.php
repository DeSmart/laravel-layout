<?php namespace DeSmart\Layout;

use Illuminate\Container\Container;

class Layout {

  private $container;

  public function __construct(Container $container) {
    $this->container = $container;
  }

  /**
   * Try to dispatch controller
   *
   * @param string $controller
   * @param array|null $args named method arguments
   * @return mixed content returned by method
   * @throws \RuntimeException when insufficient number of arguments was passed
   */
  public function dispatch($controller, array $args = null) {

    if(false === strstr($controller, '@')) {
      $class = $controller;
    }
    else {
      list($class, $method) = explode('@', $controller);
    }

    // default method
    if(true === empty($method)) {
      $method = 'execute';
    }

    $object = $this->container->make($class);
    $reflection = new \ReflectionObject($object);
    $reflected_method = $reflection->getMethod($method);

    $call_args = $this->prepareArguments($reflected_method, $args);

    return call_user_func_array(array($object, $method), array_values($call_args));
  }

  private function prepareArguments(\ReflectionMethod $method, array $args = null) {
    $arguments = array();

    if(null === $args) {
      $args = array();
    }

    foreach($method->getParameters() as $param) {
      $name = $param->name;

      if(true === array_key_exists($name, $args)) {
        $arguments[$name] = $args[$name];
      }
      else if (true === $param->isDefaultValueAvailable()) {
        $arguments[$name] = $param->getDefaultValue();
      }
      else {
        throw new \RuntimeException("Argument [{$name}] is required");
      }
    }

    return $arguments;
  }

}
