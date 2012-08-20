<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfDebug provides some method to help debugging a symfony application.
 *
 * @package    symfony
 * @subpackage debug
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfDebug.class.php 3492 2007-02-18 09:10:54Z fabien $
 */
class sfDebug
{
  /**
   * Returns PHP information as an array.
   *
   * @return array An array of php information
   */
  public static function phpInfoAsArray()
  {
    $values = array(
      'php'        => phpversion(),
      'os'         => php_uname(),
      'extensions' => get_loaded_extensions(),
    );

    return $values;
  }

  /**
   * Returns PHP globals variables as a sorted array.
   *
   * @return array PHP globals
   */
  public static function globalsAsArray()
  {
    $values = array();
    foreach (array('cookie', 'server', 'get', 'post', 'files', 'env', 'session') as $name)
    {
      if (!isset($GLOBALS['_'.strtoupper($name)]))
      {
        continue;
      }

      $values[$name] = array();
      foreach ($GLOBALS['_'.strtoupper($name)] as $key => $value)
      {
        $values[$name][$key] = $value;
      }
      ksort($values[$name]);
    }

    ksort($values);

    return $values;
  }

  /**
   * Returns sfConfig variables as a sorted array.
   *
   * @return array sfConfig variables
   */
  public static function settingsAsArray()
  {
    $config = sfConfig::getAll();

    ksort($config);

    return $config;
  }

  /**
   * Returns request parameter holders as an array.
   *
   * @param sfRequest A sfRequest instance
   *
   * @return array The request parameter holders
   */
  public static function requestAsArray($request)
  {
    if ($request)
    {
      $values = array(
        'parameterHolder' => self::flattenParameterHolder($request->getParameterHolder()),
        'attributeHolder' => self::flattenParameterHolder($request->getAttributeHolder()),
      );
    }
    else
    {
      $values = array('parameterHolder' => array(), 'attributeHolder' => array());
    }

    return $values;
  }

  /**
   * Returns response parameters as an array.
   *
   * @param sfResponse A sfResponse instance
   *
   * @return array The response parameters
   */
  public static function responseAsArray($response)
  {
    if ($response)
    {
      $values = array(
        'cookies'         => array(),
        'httpHeaders'     => array(),
        'parameterHolder' => self::flattenParameterHolder($response->getParameterHolder()),
      );
      if (method_exists($response, 'getHttpHeaders'))
      {
        foreach ($response->getHttpHeaders() as $key => $value)
        {
          $values['httpHeaders'][$key] = $value;
        }
      }

      $cookies = array();
      foreach ($response->getCookies() as $key => $value)
      {
        $values['cookies'][$key] = $value;
      }
    }
    else
    {
      $values = array('cookies' => array(), 'httpHeaders' => array(), 'parameterHolder' => array());
    }

    return $values;
  }

  /**
   * Returns a parameter holder as an array.
   *
   * @param sfParameterHolder A sfParameterHolder instance
   *
   * @return array The parameter holder as an array
   */
  public static function flattenParameterHolder($parameterHolder)
  {
    $values = array();
    foreach ($parameterHolder->getNamespaces() as $ns)
    {
      $values[$ns] = array();
      foreach ($parameterHolder->getAll($ns) as $key => $value)
      {
        $values[$ns][$key] = $value;
      }
      ksort($values[$ns]);
    }

    ksort($values);

    return $values;
  }
}
