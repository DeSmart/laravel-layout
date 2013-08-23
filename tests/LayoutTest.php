<?php

use DeSmart\Layout\Layout;
use Illuminate\Container\Container;

class DeSmartLayoutLayoutTest extends PHPUnit_Framework_TestCase {

  public function setUp() {
    require_once __DIR__.'/stubs/DispatchStub.php';
  }

  public function testDispatchWithFullArguments() {
    $layout = new Layout(new Container());
    $args = array(
      'name' => 'Hans',
      'title' => 'herr',
      'age' => 43,
    );

    $this->assertEquals($args, $layout->dispatch('DeSmartLayoutStubsDispatchStub@person', $args));
  }

  public function testDispatchWithRequiredArguments() {
    $layout = new Layout(new Container());
    $args = array(
      'name' => 'Hans',
      'age' => 43,
    );

    $expected = array(
      'name' => $args['name'],
      'age' => 43,
      'title' => 'sir',
    );

    $this->assertEquals($expected, $layout->dispatch('DeSmartLayoutStubsDispatchStub@person', $args));
  }

  public function testIfDispatchThrowsExceptionOnMissingArguments() {
    $layout = new Layout(new Container());

    $this->setExpectedException('\RuntimeException');
    $layout->dispatch('DeSmartLayoutStubsDispatchStub@person', array('name' => 'Hans'));
  }

  public function testDispatchWithNoneArguments() {
    $layout = new Layout(new Container());

    $this->assertTrue($layout->dispatch('DeSmartLayoutStubsDispatchStub@emptyPerson'));
  }

  public function testIfDefaultMethodIsCalled() {
    $layout = new Layout(new Container());

    $this->assertTrue($layout->dispatch('DeSmartLayoutStubsDispatchStub'));
  }

}
