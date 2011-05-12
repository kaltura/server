<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfYamlConfigHandler is a base class for YAML (.yml) configuration handlers. This class
 * provides a central location for parsing YAML files and detecting required categories.
 *
 * @package    symfony
 * @subpackage config
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfYamlConfigHandler.class.php 3203 2007-01-09 18:32:54Z fabien $
 */
abstract class sfYamlConfigHandler extends sfConfigHandler
{
  protected
    $yamlConfig = null;

  /**
   * Parses an array of YAMLs files and merges them in one configuration array.
   *
   * @param array An array of configuration file paths
   *
   * @param array A merged configuration array
   */
  protected function parseYamls($configFiles)
  {
    $config = array();
    foreach ($configFiles as $configFile)
    {
      $config = sfToolkit::arrayDeepMerge($config, $this->parseYaml($configFile));
    }

    return $config;
  }

  /**
   * Parses a YAML (.yml) configuration file.
   *
   * @param string An absolute filesystem path to a configuration file
   *
   * @return string A parsed .yml configuration
   *
   * @throws sfConfigurationException If a requested configuration file does not exist or is not readable
   * @throws sfParseException If a requested configuration file is improperly formatted
   */
  protected function parseYaml($configFile)
  {
    if (!is_readable($configFile))
    {
      // can't read the configuration
      $error = sprintf('Configuration file "%s" does not exist or is not readable', $configFile);

      throw new sfConfigurationException($error);
    }

    // parse our config
    $config = sfYaml::load($configFile);

    if ($config === false || $config === null)
    {
      // configuration couldn't be parsed
      $error = sprintf('Configuration file "%s" could not be parsed', $configFile);
      throw new sfParseException($error);
    }

    // get a list of the required categories
    $categories = $this->getParameterHolder()->get('required_categories', array());
    foreach ($categories as $category)
    {
      if (!isset($config[$category]))
      {
        $error = sprintf('Configuration file "%s" is missing "%s" category', $configFile, $category);
        throw new sfParseException($error);
      }
    }

    return $config;
  }

  /**
   * Merges configuration values for a given key and category.
   *
   * @param string The key name
   * @param string The category name
   *
   * @return string The value associated with this key name and category
   */
  protected function mergeConfigValue($keyName, $category)
  {
    $values = array();

    if (isset($this->yamlConfig['all'][$keyName]) && is_array($this->yamlConfig['all'][$keyName]))
    {
      $values = $this->yamlConfig['all'][$keyName];
    }

    if ($category && isset($this->yamlConfig[$category][$keyName]) && is_array($this->yamlConfig[$category][$keyName]))
    {
      $values = array_merge($values, $this->yamlConfig[$category][$keyName]);
    }

    return $values;
  }

  /**
   * Gets a configuration value for a given key and category.
   *
   * @param string The key name
   * @param string The category name
   * @param string The default value
   *
   * @return string The value associated with this key name and category
   */
  protected function getConfigValue($keyName, $category, $defaultValue = null)
  {
    if (isset($this->yamlConfig[$category][$keyName]))
    {
      return $this->yamlConfig[$category][$keyName];
    }
    else if (isset($this->yamlConfig['all'][$keyName]))
    {
      return $this->yamlConfig['all'][$keyName];
    }

    return $defaultValue;
  }
}
