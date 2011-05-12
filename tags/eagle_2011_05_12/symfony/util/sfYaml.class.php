<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfYaml class.
 *
 * @package    symfony
 * @subpackage util
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfYaml.class.php 3378 2007-02-01 06:47:15Z fabien $
 */
class sfYaml
{
  /**
   * Load YAML into a PHP array statically
   *
   * The load method, when supplied with a YAML stream (string or file),
   * will do its best to convert YAML in a file into a PHP array.
   *
   *  Usage:
   *  <code>
   *   $array = sfYAML::Load('config.yml');
   *   print_r($array);
   *  </code>
   *
   * @return array
   * @param string $input Path of YAML file or string containing YAML
   */
  public static function load($input)
  {
    $input = self::getIncludeContents($input);

    // if an array is returned by the config file assume it's in plain php form else in yaml
    if (is_array($input))
    {
      return $input;
    }

    // syck is prefered over spyc
    if (function_exists('syck_load'))
    {
      $retval = syck_load($input);

      return is_array($retval) ? $retval : array();
    }
    else
    {
      require_once(dirname(__FILE__).'/Spyc.class.php');

      $spyc = new Spyc();

      return $spyc->load($input);
    }
  }

  /**
   * Dump YAML from PHP array statically
   *
   * The dump method, when supplied with an array, will do its best
   * to convert the array into friendly YAML.
   *
   * @return string
   * @param array $array PHP array
   */
  public static function dump($array)
  {
    require_once(dirname(__FILE__).'/Spyc.class.php');

    $spyc = new Spyc();

    return $spyc->dump($array);
  }

  protected static function getIncludeContents($input)
  {
    // if input is a file, process it
    if (strpos($input, "\n") === false && is_file($input))
    {
      ob_start();
      $retval = include($input);
      $contents = ob_get_clean();

      // if an array is returned by the config file assume it's in plain php form else in yaml
      return is_array($retval) ? $retval : $contents;
    }

    // else return original input
    return $input;
  }
}

/**
 * Wraps echo to automatically provide a newline
 */
function echoln($string)
{
  echo $string."\n";
}
