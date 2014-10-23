<?php namespace DeSmart\Layout;

use Illuminate\View\Factory;
use Illuminate\Support\Contracts\RenderableInterface as Renderable;

class LazyView implements \ArrayAccess, Renderable {

  protected $environment;

  /**
   * View name
   *
   * @var string
   */
  protected $view;

  /**
   * Variables passed to view
   *
   * @var array
   */
  protected $data = array();

  public function __construct($view) {
    $this->view = $view;
  }

  public function setEnvironment(Factory $environment) {
    $this->environment = $environment;
  }

  public function offsetExists($key) {
    return array_key_exists($key, $this->data);
  }

  public function offsetGet($key) {
    return $this->data[$key];
  }

  public function offsetSet($key, $value) {
    $this->with($key, $value);
  }

  public function offsetUnset($key) {
    unset($this->data[$key]);
  }

  public function __get($key) {
    return $this->data[$key];
  }

  public function __set($key, $value) {
    $this->with($key, $value);
  }

  public function __isset($key) {
    return isset($this->data[$key]);
  }

  public function __unset($key) {
    unset($this->data[$key]);
  }

  public function __toString() {
    return $this->render();
  }

  /**
   * @param string|array $key
   * @param mixed $value
   * @return \DeSmart\Layout\LazyView
   */
  public function with($key, $value = null) {

    if (is_array($key)) {
      $this->data = array_merge($this->data, $key);
    }
    else {
      $this->data[$key] = $value;
    }

    return $this;
  }

  public function render() {
    return $this->environment->make($this->view, $this->data)
      ->render();
  }

}
