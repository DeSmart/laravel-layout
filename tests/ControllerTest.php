<?php

use Illuminate\Container\Container;
use DeSmartLayoutStubsControllerStub as Controller;
use Mockery as m;

class DeSmartLayoutControllerTest extends PHPUnit_Framework_TestCase {

  public static function setUpBeforeClass() {
    require_once __DIR__.'/stubs/ControllerStub.php';
  }

  public function tearDown() {
    m::close();
  }

  public function testExecuteProcess() {
    $c = new Container;
    $router = $this->routerFactory($args = array('foo', 'bar'));
    $view = m::mock('Illuminate\View\View');
    $view->shouldReceive('make')->once()->with('test')->andReturn(array());
    $layout = m::mock('DeSmart\Layout\Layout');
    $layout->shouldReceive('dispatch')->once()->with('Top\First', $args)->andReturn('first');
    $layout->shouldReceive('dispatch')->once()->with('Top\Second', $args)->andReturn('second');
    $layout->shouldReceive('dispatch')->once()->with('Bottom\First', $args)->andReturn('bottom first');

    $c['view'] = $view;
    $c['layout'] = $layout;
    $c['router'] = $router;

    $expected = array(
      'top' => "first\nsecond",
      'bottom' => 'bottom first',
    );

    $controller = new Controller;
    $controller->setContainer($c);

    // create layout instance manually, normally callAction() is responsible for this
    $controller->setupLayout();

    $this->assertEquals($expected, $controller->execute());
  }

  public function testExecuteProcessWithPassedArguments() {
    $c = new Container;
    $args = array('foo', 'bar');
    $view = m::mock('Illuminate\View\View');
    $view->shouldReceive('make')->once()->with('test')->andReturn(array());
    $layout = m::mock('DeSmart\Layout\Layout');
    $layout->shouldReceive('dispatch')->once()->with('Top\First', $args)->andReturn('first');
    $layout->shouldReceive('dispatch')->once()->with('Top\Second', $args)->andReturn('second');
    $layout->shouldReceive('dispatch')->once()->with('Bottom\First', $args)->andReturn('bottom first');

    $c['view'] = $view;
    $c['layout'] = $layout;

    $expected = array(
      'top' => "first\nsecond",
      'bottom' => 'bottom first',
    );

    $controller = new Controller;
    $controller->setContainer($c);

    // create layout instance manually, normally callAction() is responsible for this
    $controller->setupLayout();

    $this->assertEquals($expected, $controller->execute($args));
  }

  public function testIfRenderableResponseIsRendered() {
    $c = new Container;
    $router = $this->routerFactory($args = array('foo', 'bar'));
    $view = m::mock('Illuminate\View\View');
    $renderable = m::mock('Illuminate\Support\Contracts\RenderableInterface');
    $renderable->shouldReceive('render')->once();
    $layout = m::mock('DeSmart\Layout\Layout');
    $layout->shouldReceive('dispatch')->once()->with('Top\Render', $args)->andReturn($renderable);

    $c['view'] = $view;
    $c['layout'] = $layout;
    $c['router'] = $router;

    $controller = new Controller;
    $controller->setContainer($c);

    $controller->showOne('Top\Render');
  }

  public function testIfProfilerIsCalled() {
    $c = new Container;
    $router = $this->routerFactory($args = array('foo', 'bar'));
    $view = m::mock('Illuminate\View\View');
    $layout = m::mock('DeSmart\Layout\Layout');
    $layout->shouldReceive('dispatch')->once()->with('Top\First', $args);
    $layout->shouldReceive('dispatch')->once()->with('Top\Second', $args);
    $layout->shouldReceive('dispatch')->once()->with('Bottom\First', $args);
    $profiler = m::mock('stdClass');
    $profiler->shouldReceive('startTimer')->once()->with('Top\First');
    $profiler->shouldReceive('endTimer')->once()->with('Top\First');
    $profiler->shouldReceive('startTimer')->once()->with('Top\Second');
    $profiler->shouldReceive('endTimer')->once()->with('Top\Second');
    $profiler->shouldReceive('startTimer')->once()->with('Bottom\First');
    $profiler->shouldReceive('endTimer')->once()->with('Bottom\First');

    $c['view'] = $view;
    $c['layout'] = $layout;
    $c['router'] = $router;
    $c['profiler'] = $profiler;

    $controller = new Controller;
    $controller->setContainer($c);

    $controller->execute();
  }

  public function testIfRedirectResponseIsReturnedDirectly() {
    $c = new Container;
    $router = $this->routerFactory($args = array('foo', 'bar'));
    $view = m::mock('Illuminate\View\View');
    $redirect_response = m::mock('Symfony\Component\HttpFoundation\RedirectResponse');
    $layout = m::mock('DeSmart\Layout\Layout');
    $layout->shouldReceive('dispatch')->once()->with('Top\Render', $args)->andReturn($redirect_response);

    $c['view'] = $view;
    $c['layout'] = $layout;
    $c['router'] = $router;

    $controller = new Controller;
    $controller->setContainer($c);

    $this->assertEquals($redirect_response, $controller->showOne('Top\Render'));
  }

  private function routerFactory($args) {
    $router = m::mock('Illuminate\Routing\Router');
    $router->shouldReceive('getCurrentRoute')->once()->andReturn($router);
    $router->shouldReceive('getParametersWithoutDefaults')->once()->andReturn($args);

    return $router;
  }

}
