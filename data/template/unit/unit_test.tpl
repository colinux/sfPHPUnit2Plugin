<?php
require_once dirname(__FILE__).'{path_to_bootstrap}';

class unit_{test_class}Test extends sfPHPUnitBaseTestCase
{
  public function testDefault()
  {
    $t = $this->getTest();

    // lime-like assertions
    //$t->diag('hello world');
    //$t->ok(true, 'test something');

    // native assertions
    //$this->assertTrue(true, 'test something');
  }
}
