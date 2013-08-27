<?php

use DeSmart\Layout\Controller;

class DeSmartLayoutStubsControllerStub extends Controller {

  protected $layout = 'test';

  protected $structure = array(
    'top' => array(
      'Top\First',
      'Top\Second',
    ),
    'bottom' => array(
      'Bottom\First',
    ),
  );

  public function showOne($action) {
    $this->structure = array(
      'top' => array($action),
    );

    return $this->execute();
  }

  public function setupLayout() {
    parent::setupLayout();
  }

}
