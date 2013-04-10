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

  public static function to($path) {
    $factory = function() use ($path) {
      return \Redirect::to($path);
    };

    throw new static($factory);
  }

  public static function route($path) {
    $factory = function() use ($path) {
      return \Redirect::route($path);
    };

    throw new static($factory);
  }

  public function createRedirect() {
    $factory = $this->factory;

    return $factory();
  }

}
