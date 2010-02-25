<?php

/*
 * This file is part of the sfPHPUnit2Plugin package.
 * (c) 2010 Frank Stelzer <dev@frankstelzer.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Base class for generating PHPUnit test files.
 *
 * @package    sfPHPUnit2Plugin
 * @subpackage task
 *
 * @author     Frank Stelzer <dev@frankstelzer.de>
 */
abstract class sfPHPUnitGenerateBaseTask extends sfBaseTask
{
  /**
   * Returns plugin dir of sfPHPUnit2Plugin
   *
   * @return string
   */
  protected function getPluginDir()
  {
    return sfConfig::get('sf_plugins_dir').'/sfPHPUnit2Plugin';
  }

  /**
   * Returns the test dir to the PHPUnit test cases
   *
   * @return string
   */
  protected function getTestDir()
  {
    return sfConfig::get('sf_root_dir').'/test/phpunit';
  }

  /**
   * Creates the bootstrap files needed in the PHPUnit test cases
   *
   * @return bool
   */
  protected function createBootstrap()
  {
    $bootstrapDir = $this->getTestDir().'/bootstrap';
    $templateDir = $this->getPluginDir().'/data/template';

    // does bootstrap dir already exists?
    if (!file_exists($bootstrapDir))
    {
      // create bootstrap dir and copy bootstrap files there
      mkdir($bootstrapDir, 0755, true);
      copy($templateDir.'/unit/bootstrap.tpl', $bootstrapDir.'/unit.php');
      copy($templateDir.'/functional/bootstrap.tpl', $bootstrapDir.'/functional.php');
    }

    return true;
  }

  /**
   * Fetches template and returns its raw content
   *
   * @param string $templateName
   *
   * @throws sfCommandException when template is not found
   *
   * @return string
   */
  protected function getTemplate( $templateName )
  {
    $templatePath = $this->getPluginDir().'/data/template/' . $templateName;
    if (!file_exists($templatePath))
    {
      throw new sfCommandException(sprintf('Template "%s" does not exist.', $templateName));
    }

    return file_get_contents($templatePath);
  }

  /**
   * Renders a template and parses assigned vars into according placeholders
   *
   * @param string $content the template content
   * @param array $replacePairs
   *
   * @return string
   */
  protected function renderTemplate($content, array $replacePairs)
  {
    return strtr( $content, $replacePairs );
  }

  /**
   * Saves a rendered template to a given target file
   *
   * @param string $content the rendered template content
   * @param string $targetFile the target file
   *
   * @return int number of bytes that were written to the file, or FALSE on failure.
   */
  protected function saveFile($content, $targetFile, array $options)
  {
    $completeTarget = $this->getTestDir().'/'.$targetFile;
    $dir = dirname($completeTarget);
    if (!file_exists($dir))
    {
      $this->logSection('dir+', $dir);
      mkdir($dir, 0755, true);
    }
    $this->logSection('file+', $completeTarget);

    if (file_exists($completeTarget) && !$options['overwrite'])
    {
      throw new sfCommandException(sprintf('Test case "%s" does already exist. Use the overwrite option to force overwritting.', $targetFile));
    }

    return file_put_contents($completeTarget, $content);
  }
}
