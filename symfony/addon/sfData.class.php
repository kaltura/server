<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * This class defines the interface for interacting with data, as well
 * as default implementations.
 *
 * @package    symfony
 * @subpackage addon
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfData.class.php 3382 2007-02-01 07:34:56Z fabien $
 */

abstract class sfData
{
  protected
    $deleteCurrentData = true,
    $object_references = array();

  /**
   * Sets a flag to indicate if the current data in the database
   * should be deleted before new data is loaded.
   *
   * @param boolean The flag value
   */
  public function setDeleteCurrentData($boolean)
  {
    $this->deleteCurrentData = $boolean;
  }

  /**
   * Gets the current value of the flag that indicates whether
   * current data is to be deleted or not.
   *
   * @returns boolean
   */
  public function getDeleteCurrentData()
  {
    return $this->deleteCurrentData;
  }

  /**
   * Loads data for the database from a YAML file
   *
   * @param string The path to the YAML file.
   */
  protected function doLoadDataFromFile($fixture_file)
  {
    // import new datas
    $data = sfYaml::load($fixture_file);

    $this->loadDataFromArray($data);
  }

  /**
   * Manages the insertion of data into the data source
   *
   * @param array The data to be inserted into the data source
   */
  abstract public function loadDataFromArray($data);

  /**
   * Manages reading all of the fixture data files and
   * loading them into the data source
   *
   * @param array The path names of the YAML data files
   */
  protected function doLoadData($fixture_files)
  {
    $this->object_references = array();
    $this->maps = array();

    sort($fixture_files);
    foreach ($fixture_files as $fixture_file)
    {
      $this->doLoadDataFromFile($fixture_file);
    }
  }

  /**
   * Gets a list of one or more *.yml files and returns the list in an array
   *
   * @param string A directory or file name; if null, then defaults to 'sf_data_dir'/fixtures
   *
   * @returns array A list of *.yml files.
   *
   * @throws sfInitializationException If the directory or file does not exist.
   */
  protected function getFiles($directory_or_file = null)
  {
    // directory or file?
    $fixture_files = array();
    if (!$directory_or_file)
    {
      $directory_or_file = sfConfig::get('sf_data_dir').'/fixtures';
    }

    if (is_file($directory_or_file))
    {
      $fixture_files[] = $directory_or_file;
    }
    else if (is_dir($directory_or_file))
    {
      $fixture_files = sfFinder::type('file')->name('*.yml')->in($directory_or_file);
    }
    else
    {
      throw new sfInitializationException('You must give a directory or a file.');
    }

    return $fixture_files;
  }
}
