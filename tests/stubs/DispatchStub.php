<?php

class DeSmartLayoutStubsDispatchStub {

  public function person($name, $age, $title = 'sir') {
    return compact('name', 'age', 'title');
  }

  public function emptyPerson($name = null) {
    return true;
  }

  public function execute() {
    return true;
  }

}
