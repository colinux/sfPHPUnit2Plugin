<?php
require_once dirname(__FILE__).'{path_to_bootstrap}';

class selenium_{controller_class}ActionsTest extends sfPHPUnitBaseSeleniumTestCase
{
  protected function setUp()
  {
    // $this->setBrowser('*firefox');
    // $this->setBrowserUrl('http://localhost/{controller_name}/');
  }

  public function testTitle()
  {
    // $this->open('http://localhost/{controller_name}/');
    // $this->assertTitle('Example WWW Page');
  }
}