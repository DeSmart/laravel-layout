<?php

use Mockery as m;
use DeSmart\Layout\LazyView;

class DeSmartLayoutLazyViewTest extends PHPUnit_Framework_TestCase {

  public function tearDown() {
    m::close();
  }

  public function testArrayAccess() {
    $view = new LazyView('test');
    $view['a'] = 'a';
    $view['b'] = 'b';

    unset($view['b']);

    $this->assertEquals('a', $view['a']);
    $this->assertFalse(isset($view['b']));
  }

  public function testGetters() {
    $view = new LazyView('test');
    $view->a = 'a';
    $this->b = 'b';

    unset($this->b);

    $this->assertEquals('a', $view->a);
    $this->assertFalse(isset($view->b));
  }

  public function testRenderProcess() {
    $view = new LazyView($name = 'test');
    $view->with($data = array(
      'foo' => 'bar',
    ));

    $view->setEnvironment($env = $this->viewFactory($name, $data));
    $env->shouldReceive('render')->once();

    $view->render();
  }

  public function testIfProxyIsRenderable() {
    $view = new LazyView('test');

    $this->assertInstanceOf('Illuminate\Support\Contracts\RenderableInterface', $view);
  }

  protected function viewFactory($name, $data) {
    $mock = m::mock('Illuminate\View\Environment');
    $mock->shouldReceive('make')->once()->with($name, $data)->andReturn($mock);

    return $mock;
  }

}
