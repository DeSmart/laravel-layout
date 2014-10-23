<?php

use Mockery as m;
use DeSmart\Layout\LazyView;

class DeSmartLayoutLazyViewTest extends PHPUnit_Framework_TestCase {

  public function setUp() {
    m::getConfiguration()->allowMockingNonExistentMethods(false);
  }

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

    $view->render();
  }

  public function testIfProxyIsRenderable() {
    $view = new LazyView('test');

    $this->assertInstanceOf('Illuminate\Support\Contracts\RenderableInterface', $view);
  }

  protected function viewFactory($name, $data) {
    $view = m::mock('Illuminate\View\View');
    $view->shouldReceive('render')->once()->andReturn('');
    $mock = m::mock('Illuminate\View\Factory');
    $mock->shouldReceive('make')->once()->with($name, $data)->andReturn($view);

    return $mock;
  }

}
