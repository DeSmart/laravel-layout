<?php namespace DeSmart\Layout\Facades;

use Illuminate\Support\Facades\Facade;

class Layout extends Facade {

  protected static function getFacadeAccessor() {
    return 'layout';
  }

}
