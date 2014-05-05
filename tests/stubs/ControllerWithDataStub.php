<?php

use DeSmart\Layout\Controller;

class DeSmartLayoutStubsControllerWithDataStub extends Controller {

  protected $layout = 'test';

  protected $data = array(
    'main_class' => 'foo',
  );

  protected $structure = array(
    'top' => array(
      'Top\First',
    ),
  );

  public function setupLayout() {
    parent::setupLayout();
  }

}
