<?php namespace DeSmartTests\Layout;

use DeSmart\Layout\Layout;
use Illuminate\Container\Container;

class LayoutTest extends \PHPUnit_Framework_TestCase {

  public function testDispatchWithFullArguments() {
    $layout = new Layout(new Container());
    $args = array(
      'name' => 'Hans',
      'title' => 'herr',
      'age' => 43,
    );

    $this->assertEquals($args, $layout->dispatch('\DeSmartTests\Layout\Layout\TestController@person', $args));
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

    $this->assertEquals($expected, $layout->dispatch('\DeSmartTests\Layout\Layout\TestController@person', $args));
  }

  public function testIfDispatchThrowsExceptionOnWrongControllerFormat() {
    $layout = new Layout(new Container());

    $this->setExpectedException('\InvalidArgumentException');
    $layout->dispatch('Class::foo');
  }

  public function testIfDispatchThrowsExceptionOnMissingArguments() {
    $layout = new Layout(new Container());

    $this->setExpectedException('\RuntimeException');
    $layout->dispatch('\DeSmartTests\Layout\Layout\TestController@person', array('name' => 'Hans'));
  }

  public function testDispatchWithNoneArguments() {
    $layout = new Layout(new Container());

    $this->assertTrue($layout->dispatch('\DeSmartTests\Layout\Layout\TestController@emptyPerson'));
  }

}
