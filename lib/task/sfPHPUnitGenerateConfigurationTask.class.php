<?php

/*
 * This file is part of the sfPHPUnit2Plugin package.
 * (c) 2010 Frank Stelzer <dev@frankstelzer.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Generates a PHPUnit test file for unit tests.
 *
 * @package    sfPHPUnit2Plugin
 * @subpackage task
 *
 * @author     Frank Stelzer <dev@frankstelzer.de>
 */
class sfPHPUnitGenerateConfigurationTestTask extends sfPHPUnitGenerateBaseTask
{
  /**
   * @see sfTask
   */
  protected function configure()
  {
    /*
    $this->addArguments(array(
    ));
    */

    $this->addOptions(array(
      new sfCommandOption('overwrite', null, sfCommandOption::PARAMETER_NONE, 'Forces the task to overwrite any existing configuration file'),
    ));

    $this->namespace        = 'phpunit';
    $this->name             = 'generate-configuration';
    $this->briefDescription = 'Generates the configuration xml for unit tests';
    $this->detailedDescription = <<<EOF
The [phpunit:generate-configuration|INFO] generates the default configuration xml for unit tests, which is lateron used by PHPUnit

Call it with:

  [php symfony phpunit:generate-configuration|INFO]
EOF;
  }

   /**
   * @see sfTask
   */
  protected function execute($arguments = array(), $options = array())
  {
    $this->createBootstrap();

    $template = $this->getTemplate('phpunit.xml.dist.tpl');

    $dist_file_path = sfConfig::get('sf_root_dir').'/phpunit.xml.dist';
    $prod_file_path = sfConfig::get('sf_root_dir').'/phpunit.xml';
    $replacePairs = array();
    
    $test_directories = $this->getTestDirectories();

	  $unit_xml = $this->getUnitPathXML($test_directories);
	  $functional_xml = $this->getFunctionalPathXML($test_directories);
	  $selenium_xml = $this->getSeleniumPathXML($test_directories);

    $replacePairs['{testsuite_xml}'] = trim($unit_xml.$functional_xml.$selenium_xml, "\r\n");

    $rendered = $this->renderTemplate($template, $replacePairs);

    file_put_contents($dist_file_path, $rendered);
    $this->logSection('file+', basename($dist_file_path));

    if(!file_exists($prod_file_path) || $options['overwrite'])
    {
      file_put_contents($prod_file_path, $rendered);
      $this->logSection('file+', basename($prod_file_path));
    }
  }

  protected function getUnitPathXML($directories)
  {
    $paths = $this->getPHPUnitPaths($directories, 'unit');
    $return = $this->getPHPUnitPathXML('Unit Tests', $paths);
    return $return;
  }

  protected function getFunctionalPathXML($directories)
  {
    $paths = $this->getPHPUnitPaths($directories, 'functional');
    $return = $this->getPHPUnitPathXML('Functional Tests', $paths);
    return $return;
  }
  
  protected function getSeleniumPathXML($directories)
  {
    $paths = $this->getPHPUnitPaths($directories, 'selenium');
    $return = $this->getPHPUnitPathXML('Selenium Tests', $paths);
    return $return;
  }
  
  protected function getPHPUnitPathXML($name, $directories)
  {
    $out = '    <testsuite name="'.$name.'">'."\r\n";
    if (count($directories) > 0)
    {
      foreach($directories as $dir)
      {
        $path = $dir;
        $out .= '      <directory>'.$path.'</directory>'."\r\n";
      }
    }
    $out .= '    </testsuite>'."\r\n";
    return $out;
  }
  
  protected function getPHPUnitPaths($directories, $type = 'unit')
  {
    $return = array();
	  foreach($directories as $dir)
	  {
		  $path = $this->checkPHPUnitPath($dir, $type);
		  if ($path)
		  {
        $return[] = str_replace(sfConfig::get('sf_root_dir').DIRECTORY_SEPARATOR, '', $path);
		  }
	  }
	  return $return;
  }

  protected function checkPHPUnitPath($path, $type = 'unit')
  {
    $path = $path.DIRECTORY_SEPARATOR.'test'.DIRECTORY_SEPARATOR.'phpunit'.DIRECTORY_SEPARATOR.$type;
    if (file_exists($path) && is_dir($path))
      return $path;
    return false;
  }
}
