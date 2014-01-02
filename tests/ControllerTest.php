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
    $view->shouldReceive('render')->once();
    $env = m::mock('Illuminate\View\Environment');
    $env->shouldReceive('make')->once()->with('test', array('top' => "first\nsecond", 'bottom' => "bottom first"))->andReturn($view);
    $layout = m::mock('DeSmart\Layout\Layout');
    $layout->shouldReceive('dispatch')->once()->with('Top\First', $args)->andReturn('first');
    $layout->shouldReceive('dispatch')->once()->with('Top\Second', $args)->andReturn('second');
    $layout->shouldReceive('dispatch')->once()->with('Bottom\First', $args)->andReturn('bottom first');

    $c['view'] = $env;
    $c['layout'] = $layout;
    $c['router'] = $router;

    $controller = new Controller;
    $controller->setContainer($c);

    // create layout instance manually, normally callAction() is responsible for this
    $controller->setupLayout();
    $proxy = $controller->execute();

    $this->assertInstanceOf('DeSmart\Layout\LazyView', $proxy);
    $proxy->render();
  }

  public function testExecuteProcessWithPassedArguments() {
    $c = new Container;
    $args = array('foo', 'bar');
    $view = m::mock('Illuminate\View\View');
    $view->shouldReceive('render')->once();
    $env = m::mock('Illuminate\View\Environment');
    $env->shouldReceive('make')->once()->with('test', array('top' => "first\nsecond", 'bottom' => "bottom first"))->andReturn($view);
    $layout = m::mock('DeSmart\Layout\Layout');
    $layout->shouldReceive('dispatch')->once()->with('Top\First', $args)->andReturn('first');
    $layout->shouldReceive('dispatch')->once()->with('Top\Second', $args)->andReturn('second');
    $layout->shouldReceive('dispatch')->once()->with('Bottom\First', $args)->andReturn('bottom first');

    $c['view'] = $env;
    $c['layout'] = $layout;

    $controller = new Controller;
    $controller->setContainer($c);

    // create layout instance manually, normally callAction() is responsible for this
    $controller->setupLayout();
    $controller->execute($args)->render();
  }

  public function testIfRenderableResponseIsRendered() {
    $c = new Container;
    $router = $this->routerFactory($args = array('foo', 'bar'));
    $view = m::mock('Illuminate\View\View');
    $view->shouldReceive('render')->once();
    $env = m::mock('Illuminate\View\Environment');
    $env->shouldReceive('make')->once()->with('test', array('top' => ''))->andReturn($view);
    $renderable = m::mock('Illuminate\Support\Contracts\RenderableInterface');
    $renderable->shouldReceive('render')->once()->andReturn('');
    $layout = m::mock('DeSmart\Layout\Layout');
    $layout->shouldReceive('dispatch')->once()->with('Top\Render', $args)->andReturn($renderable);

    $c['view'] = $env;
    $c['layout'] = $layout;
    $c['router'] = $router;

    $controller = new Controller;
    $controller->setContainer($c);
    $controller->setupLayout();

    $controller->showOne('Top\Render')->render();
  }

  public function testIfProfilerIsCalled() {
    $c = new Container;
    $router = $this->routerFactory($args = array('foo', 'bar'));
    $view = m::mock('Illuminate\View\View');
    $view->shouldReceive('render')->once();
    $env = m::mock('Illuminate\View\Environment');
    $env->shouldReceive('make')->once()->with('test', array('top' => "first\nsecond", 'bottom' => 'bottom first'))->andReturn($view);
    $layout = m::mock('DeSmart\Layout\Layout');
    $layout->shouldReceive('dispatch')->once()->with('Top\First', $args)->andReturn('first');
    $layout->shouldReceive('dispatch')->once()->with('Top\Second', $args)->andReturn('second');
    $layout->shouldReceive('dispatch')->once()->with('Bottom\First', $args)->andReturn('bottom first');
    $profiler = m::mock('stdClass');
    $profiler->shouldReceive('startTimer')->once()->with('Top\First');
    $profiler->shouldReceive('endTimer')->once()->with('Top\First');
    $profiler->shouldReceive('startTimer')->once()->with('Top\Second');
    $profiler->shouldReceive('endTimer')->once()->with('Top\Second');
    $profiler->shouldReceive('startTimer')->once()->with('Bottom\First');
    $profiler->shouldReceive('endTimer')->once()->with('Bottom\First');

    $c['view'] = $env;
    $c['layout'] = $layout;
    $c['router'] = $router;
    $c['profiler'] = $profiler;

    $controller = new Controller;
    $controller->setContainer($c);
    $controller->setupLayout();

    $controller->execute()->render();
  }

  public function testIfRedirectResponseIsReturnedDirectly() {
    $c = new Container;
    $router = $this->routerFactory($args = array('foo', 'bar'));
    $env = m::mock('Illuminate\View\Environment');
    $env->shouldReceive('make')->never();
    $redirect_response = m::mock('Symfony\Component\HttpFoundation\RedirectResponse');
    $layout = m::mock('DeSmart\Layout\Layout');
    $layout->shouldReceive('dispatch')->once()->with('Top\Render', $args)->andReturn($redirect_response);

    $c['view'] = $env;
    $c['layout'] = $layout;
    $c['router'] = $router;

    $controller = new Controller;
    $controller->setContainer($c);
    $controller->setupLayout();

    $this->assertEquals($redirect_response, $controller->showOne('Top\Render'));
  }

  private function routerFactory($args) {
    $router = m::mock('Illuminate\Routing\Router');
    $router->shouldReceive('getCurrentRoute')->once()->andReturn($router);
    $router->shouldReceive('getParametersWithoutDefaults')->once()->andReturn($args);

    return $router;
  }

}
