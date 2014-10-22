<?php

use Illuminate\Container\Container;
use DeSmartLayoutStubsControllerStub as Controller;
use DeSmartLayoutStubsControllerWithDataStub as ControllerWithData;
use Mockery as m;

class DeSmartLayoutControllerTest extends PHPUnit_Framework_TestCase {

  public static function setUpBeforeClass() {
    require_once __DIR__.'/stubs/ControllerStub.php';
    require_once __DIR__.'/stubs/ControllerWithDataStub.php';
  }

  public function setUp() {
    m::getConfiguration()->allowMockingNonExistentMethods(false);
  }

  public function tearDown() {
    m::close();
  }

  public function testExecuteProcess() {
    $router = $this->routerFactory($args = array('foo', 'bar'));
    $view = m::mock('Illuminate\View\View');
    $view->shouldReceive('render')->once();
    $env = m::mock('Illuminate\View\Factory');
    $env->shouldReceive('make')->once()->with('test', array('top' => "first\nsecond", 'bottom' => "bottom first"))->andReturn($view);
    $layout = m::mock('DeSmart\Layout\Layout');
    $layout->shouldReceive('dispatch')->once()->with('Top\First', $args)->andReturn('first');
    $layout->shouldReceive('dispatch')->once()->with('Top\Second', $args)->andReturn('second');
    $layout->shouldReceive('dispatch')->once()->with('Bottom\First', $args)->andReturn('bottom first');

    $controller = new Controller;

    // create layout instance manually, normally callAction() is responsible for this
    $controller->setupLayout();
    $controller->setViewFactory($env);
    $controller->setRouter($router);
    $controller->setLayoutDispatcher($layout);
    $proxy = $controller->execute();

    $this->assertInstanceOf('DeSmart\Layout\LazyView', $proxy);
    $proxy->render();
  }

  public function testExecuteProcessWhenControllerHasDataAttributeDefined() {
    $router = $this->routerFactory($args = array('foo', 'bar'));
    $view = m::mock('Illuminate\View\View');
    $view->shouldReceive('render')->once();
    $env = m::mock('Illuminate\View\Factory');
    $env->shouldReceive('make')->once()->with('test', array('top' => 'first', 'main_class' => 'foo'))->andReturn($view);
    $layout = m::mock('DeSmart\Layout\Layout');
    $layout->shouldReceive('dispatch')->once()->with('Top\First', $args)->andReturn('first');

    $controller = new ControllerWithData;

    // create layout instance manually, normally callAction() is responsible for this
    $controller->setupLayout();
    $controller->setViewFactory($env);
    $controller->setRouter($router);
    $controller->setLayoutDispatcher($layout);
    $controller->execute()
      ->render();
  }

  public function testExecuteProcessWithPassedArguments() {
    $args = array('foo', 'bar');
    $view = m::mock('Illuminate\View\View');
    $view->shouldReceive('render')->once();
    $env = m::mock('Illuminate\View\Factory');
    $env->shouldReceive('make')->once()->with('test', array('top' => "first\nsecond", 'bottom' => "bottom first"))->andReturn($view);
    $layout = m::mock('DeSmart\Layout\Layout');
    $layout->shouldReceive('dispatch')->once()->with('Top\First', $args)->andReturn('first');
    $layout->shouldReceive('dispatch')->once()->with('Top\Second', $args)->andReturn('second');
    $layout->shouldReceive('dispatch')->once()->with('Bottom\First', $args)->andReturn('bottom first');

    $controller = new Controller;
    $controller->setViewFactory($env);
    $controller->setLayoutDispatcher($layout);

    // create layout instance manually, normally callAction() is responsible for this
    $controller->setupLayout();
    $controller->execute($args)->render();
  }

  public function testIfRenderableResponseIsRendered() {
    $router = $this->routerFactory($args = array('foo', 'bar'));
    $view = m::mock('Illuminate\View\View');
    $view->shouldReceive('render')->once();
    $env = m::mock('Illuminate\View\Factory');
    $env->shouldReceive('make')->once()->with('test', array('top' => ''))->andReturn($view);
    $renderable = m::mock('Illuminate\Support\Contracts\RenderableInterface');
    $renderable->shouldReceive('render')->once()->andReturn('');
    $layout = m::mock('DeSmart\Layout\Layout');
    $layout->shouldReceive('dispatch')->once()->with('Top\Render', $args)->andReturn($renderable);

    $controller = new Controller;
    $controller->setLayoutDispatcher($layout);
    $controller->setViewFactory($env);
    $controller->setRouter($router);
    $controller->setupLayout();

    $controller->showOne('Top\Render')->render();
  }

  public function testIfRedirectResponseIsReturnedDirectly() {
    $router = $this->routerFactory($args = array('foo', 'bar'));
    $env = m::mock('Illuminate\View\Factory');
    $env->shouldReceive('make')->never();
    $redirect_response = m::mock('Symfony\Component\HttpFoundation\RedirectResponse');
    $layout = m::mock('DeSmart\Layout\Layout');
    $layout->shouldReceive('dispatch')->once()->with('Top\Render', $args)->andReturn($redirect_response);

    $controller = new Controller;
    $controller->setViewFactory($env);
    $controller->setLayoutDispatcher($layout);
    $controller->setRouter($router);
    $controller->setupLayout();

    $this->assertEquals($redirect_response, $controller->showOne('Top\Render'));
  }

  private function routerFactory($args) {
    $router = m::mock('Illuminate\Routing\Router');
    $route = m::mock('Illuminate\Routing\Route');
    $router->shouldReceive('getCurrentRoute')->once()->andReturn($route);
    $route->shouldReceive('parametersWithoutNulls')->once()->andReturn($args);

    return $router;
  }

}
