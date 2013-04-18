<?php namespace DeSmart\Layout;

/**
 * Nasty way of using exceptions to do redirects from one of the callbacks.
 *
 * Normally when \Redirect::to is done inside one of the callback
 * it's content is put inside view.
 * This makes the whole view to be rendered, send and after that actual redirect is done.
 *
 * When this Redirect exception is thrown the redirect is immediate.
 */
class Redirect extends \Exception {

  protected $factory;

  public function __construct(\Closure $factory) {
    $this->factory = $factory;
  }

  public static function to($path, $status = 302, $headers = array(), $secure = null) {
    $factory = function() use ($path, $status, $headers, $secure) {
      return \Redirect::to($path, $status, $headers, $secure);
    };

    throw new static($factory);
  }

  public static function route($route, $parameters = array(), $status = 302, $headers = array()) {
    $factory = function() use ($route, $parameters, $status, $headers) {
      return \Redirect::route($route, $parameters, $status, $headers);
    };

    throw new static($factory);
  }

  public function createRedirect() {
    $factory = $this->factory;

    return $factory();
  }

}
