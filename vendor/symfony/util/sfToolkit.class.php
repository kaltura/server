<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * (c) 2004-2006 Sean Kerr.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfToolkit provides basic utility methods.
 *
 * @package    symfony
 * @subpackage util
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @author     Sean Kerr <skerr@mojavi.org>
 * @version    SVN: $Id: sfToolkit.class.php 3251 2007-01-12 20:49:29Z fabien $
 */
class sfToolkit
{
  /**
   * Extract the class or interface name from filename.
   *
   * @param string A filename.
   *
   * @return string A class or interface name, if one can be extracted, otherwise null.
   */
  public static function extractClassName($filename)
  {
    $retval = null;

    if (self::isPathAbsolute($filename))
    {
      $filename = basename($filename);
    }

    $pattern = '/(.*?)\.(class|interface)\.php/i';

    if (preg_match($pattern, $filename, $match))
    {
      $retval = $match[1];
    }

    return $retval;
  }

  /**
   * Clear all files in a given directory.
   *
   * @param  string An absolute filesystem path to a directory.
   *
   * @return void
   */
  public static function clearDirectory($directory)
  {
    if (!is_dir($directory))
    {
      return;
    }

    // open a file point to the cache dir
    $fp = opendir($directory);

    // ignore names
    $ignore = array('.', '..', 'CVS', '.svn');

    while (($file = readdir($fp)) !== false)
    {
      if (!in_array($file, $ignore))
      {
        if (is_link($directory.'/'.$file))
        {
          // delete symlink
          unlink($directory.'/'.$file);
        }
        else if (is_dir($directory.'/'.$file))
        {
          // recurse through directory
          self::clearDirectory($directory.'/'.$file);

          // delete the directory
          rmdir($directory.'/'.$file);
        }
        else
        {
          // delete the file
          unlink($directory.'/'.$file);
        }
      }
    }

    // close file pointer
    fclose($fp);
  }

  /**
   * Clear all files and directories corresponding to a glob pattern.
   *
   * @param  string An absolute filesystem pattern.
   *
   * @return void
   */
  public static function clearGlob($pattern)
  {
    $files = glob($pattern);

    // order is important when removing directories
    sort($files);

    foreach ($files as $file)
    {
      if (is_dir($file))
      {
        // delete directory
        self::clearDirectory($file);
      }
      else
      {
        // delete file
        unlink($file);
      }
    }
  }

  /**
   * Determine if a filesystem path is absolute.
   *
   * @param path A filesystem path.
   *
   * @return bool true, if the path is absolute, otherwise false.
   */
  public static function isPathAbsolute($path)
  {
    if ($path[0] == '/' || $path[0] == '\\' ||
        (strlen($path) > 3 && ctype_alpha($path[0]) &&
         $path[1] == ':' &&
         ($path[2] == '\\' || $path[2] == '/')
        )
       )
    {
      return true;
    }

    return false;
  }

  /**
   * Determine if a lock file is present.
   *
   * @param integer A max amount of life time for the lock file.
   *
   * @return bool true, if the lock file is present, otherwise false.
   */
  public static function hasLockFile($lockFile, $maxLockFileLifeTime = 0)
  {
    $isLocked = false;
    if (is_readable($lockFile) && ($last_access = fileatime($lockFile)))
    {
      $now = time();
      $timeDiff = $now - $last_access;

      if (!$maxLockFileLifeTime || $timeDiff < $maxLockFileLifeTime)
      {
        $isLocked = true;
      }
      else
      {
        unlink($lockFile);
      }
    }

    return $isLocked;
  }

  public static function stripComments($source)
  {
    if (!sfConfig::get('sf_strip_comments', true))
    {
      return $source;
    }

    // tokenizer available?
    if (!function_exists('token_get_all'))
    {
      $source = sfToolkit::pregtr($source, array('#/\*((?!\*/)[\d\D\s])*\*/#' => '',   // remove /* ... */
                                                 '#^\s*//.*$#m'               => '')); // remove // ...

      return $source;
    }

    $output = '';

    $tokens = token_get_all($source);
    foreach ($tokens as $token)
    {
      if (is_string($token))
      {
        // simple 1-character token
        $output .= $token;
      }
      else
      {
        // token array
        list($id, $text) = $token;

        switch ($id)
        {
          case T_COMMENT:
          case T_DOC_COMMENT:
            // no action on comments
            break;
          default:
            // anything else -> output "as is"
            $output .= $text;
            break;
        }
      }
    }

    return $output;
  }

  public static function stripslashesDeep($value)
  {
    return is_array($value) ? array_map(array('sfToolkit', 'stripslashesDeep'), $value) : stripslashes($value);
  }

  // code from php at moechofe dot com (array_merge comment on php.net)
  /*
   * array arrayDeepMerge ( array array1 [, array array2 [, array ...]] )
   *
   * Like array_merge
   *
   *  arrayDeepMerge() merges the elements of one or more arrays together so
   * that the values of one are appended to the end of the previous one. It
   * returns the resulting array.
   *  If the input arrays have the same string keys, then the later value for
   * that key will overwrite the previous one. If, however, the arrays contain
   * numeric keys, the later value will not overwrite the original value, but
   * will be appended.
   *  If only one array is given and the array is numerically indexed, the keys
   * get reindexed in a continuous way.
   *
   * Different from array_merge
   *  If string keys have arrays for values, these arrays will merge recursively.
   */
  public static function arrayDeepMerge()
  {
    switch (func_num_args())
    {
      case 0:
        return false;
      case 1:
        return func_get_arg(0);
      case 2:
        $args = func_get_args();
        $args[2] = array();
        if (is_array($args[0]) && is_array($args[1]))
        {
          foreach (array_unique(array_merge(array_keys($args[0]),array_keys($args[1]))) as $key)
          {
            $isKey0 = array_key_exists($key, $args[0]);
            $isKey1 = array_key_exists($key, $args[1]);
            if ($isKey0 && $isKey1 && is_array($args[0][$key]) && is_array($args[1][$key]))
            {
              $args[2][$key] = self::arrayDeepMerge($args[0][$key], $args[1][$key]);
            }
            else if ($isKey0 && $isKey1)
            {
              $args[2][$key] = $args[1][$key];
            }
            else if (!$isKey1)
            {
              $args[2][$key] = $args[0][$key];
            }
            else if (!$isKey0)
            {
              $args[2][$key] = $args[1][$key];
            }
          }
          return $args[2];
        }
        else
        {
          return $args[1];
        }
      default :
        $args = func_get_args();
        $args[1] = sfToolkit::arrayDeepMerge($args[0], $args[1]);
        array_shift($args);
        return call_user_func_array(array('sfToolkit', 'arrayDeepMerge'), $args);
        break;
    }
  }

  public static function stringToArray($string)
  {
    preg_match_all('/
      \s*(\w+)              # key                               \\1
      \s*=\s*               # =
      (\'|")?               # values may be included in \' or " \\2
      (.*?)                 # value                             \\3
      (?(2) \\2)            # matching \' or " if needed        \\4
      \s*(?:
        (?=\w+\s*=) | \s*$  # followed by another key= or the end of the string
      )
    /x', $string, $matches, PREG_SET_ORDER);

    $attributes = array();
    foreach ($matches as $val)
    {
      $attributes[$val[1]] = self::literalize($val[3]);
    }

    return $attributes;
  }

  /**
   * Finds the type of the passed value, returns the value as the new type.
   *
   * @param  string
   * @return mixed
   */
  public static function literalize($value, $quoted = false)
  {
    // lowercase our value for comparison
    $value  = trim($value);
    $lvalue = strtolower($value);

    if (in_array($lvalue, array('null', '~', '')))
    {
      $value = null;
    }
    else if (in_array($lvalue, array('true', 'on', '+', 'yes')))
    {
      $value = true;
    }
    else if (in_array($lvalue, array('false', 'off', '-', 'no')))
    {
      $value = false;
    }
    else if (ctype_digit($value))
    {
      $value = (int) $value;
    }
    else if (is_numeric($value))
    {
      $value = (float) $value;
    }
    else
    {
      $value = self::replaceConstants($value);
      if ($quoted)
      {
        $value = '\''.str_replace('\'', '\\\'', $value).'\'';
      }
    }

    return $value;
  }

  /**
   * Replaces constant identifiers in a scalar value.
   *
   * @param string the value to perform the replacement on
   * @return string the value with substitutions made
   */
  public static function replaceConstants($value)
  {
    return is_string($value) ? preg_replace('/%(.+?)%/e', 'sfConfig::has(strtolower("\\1")) ? sfConfig::get(strtolower("\\1")) : "%\\1%"', $value) : $value;
  }

  /**
   * Returns subject replaced with regular expression matchs
   *
   * @param mixed subject to search
   * @param array array of search => replace pairs
   */
  public static function pregtr($search, $replacePairs)
  {
    return preg_replace(array_keys($replacePairs), array_values($replacePairs), $search);
  }

  public static function isArrayValuesEmpty($array)
  {
    static $isEmpty = true;
    foreach ($array as $value)
    {
      $isEmpty = (is_array($value)) ? self::isArrayValuesEmpty($value) : (strlen($value) == 0);
      if (!$isEmpty)
      {
        break;
      }
    }

    return $isEmpty;
  }

  /**
   * Check if a string is an utf8 using a W3C regular expression
   * http://fr3.php.net/manual/en/function.mb-detect-encoding.php#50087
   *
   * @param string
   *
   * @return bool true if $string is valid UTF-8 and false otherwise.
   */
  public static function isUTF8($string)
  {
    // from http://w3.org/International/questions/qa-forms-utf-8.html
    return preg_match('%^(?:
             [\x09\x0A\x0D\x20-\x7E]            # ASCII
           | [\xC2-\xDF][\x80-\xBF]             # non-overlong 2-byte
           |  \xE0[\xA0-\xBF][\x80-\xBF]        # excluding overlongs
           | [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}  # straight 3-byte
           |  \xED[\x80-\x9F][\x80-\xBF]        # excluding surrogates
           |  \xF0[\x90-\xBF][\x80-\xBF]{2}     # planes 1-3
           | [\xF1-\xF3][\x80-\xBF]{3}          # planes 4-15
           |  \xF4[\x80-\x8F][\x80-\xBF]{2}     # plane 16
       )*$%xs', $string);
  }

  public static function getArrayValueForPath($values, $name, $default = null)
  {
    if (false !== ($offset = strpos($name, '[')))
    {
      if (isset($values[substr($name, 0, $offset)]))
      {
        $array = $values[substr($name, 0, $offset)];

        while ($pos = strpos($name, '[', $offset))
        {
          $end = strpos($name, ']', $pos);
          if ($end == $pos + 1)
          {
            // reached a []
            break;
          }
          else if (!isset($array[substr($name, $pos + 1, $end - $pos - 1)]))
          {
            return $default;
          }
          $array = $array[substr($name, $pos + 1, $end - $pos - 1)];
          $offset = $end;
        }

        return $array;
      }
    }

    return $default;
  }

  public static function getPhpCli()
  {
    $path = getenv('PATH') ? getenv('PATH') : getenv('Path');
    $suffixes = DIRECTORY_SEPARATOR == '\\' ? (getenv('PATHEXT') ? explode(PATH_SEPARATOR, getenv('PATHEXT')) : array('.exe', '.bat', '.cmd', '.com')) : array('');
    foreach (array('php5', 'php') as $phpCli)
    {
      foreach ($suffixes as $suffix)
      {
        foreach (explode(PATH_SEPARATOR, $path) as $dir)
        {
          $file = $dir.DIRECTORY_SEPARATOR.$phpCli.$suffix;
          if (is_executable($file))
          {
            return $file;
          }
        }
      }
    }

    throw new sfException('Unable to find PHP executable');
  }

  /**
   * From PEAR System.php
   *
   * LICENSE: This source file is subject to version 3.0 of the PHP license
   * that is available through the world-wide-web at the following URI:
   * http://www.php.net/license/3_0.txt.  If you did not receive a copy of
   * the PHP License and are unable to obtain it through the web, please
   * send a note to license@php.net so we can mail you a copy immediately.
   *
   * @author     Tomas V.V.Cox <cox@idecnet.com>
   * @copyright  1997-2006 The PHP Group
   * @license    http://www.php.net/license/3_0.txt  PHP License 3.0
   */
  public static function getTmpDir()
  {
    if (DIRECTORY_SEPARATOR == '\\')
    {
      if ($var = isset($_ENV['TEMP']) ? $_ENV['TEMP'] : getenv('TEMP'))
      {
        return $var;
      }
      if ($var = isset($_ENV['TMP']) ? $_ENV['TMP'] : getenv('TMP'))
      {
        return $var;
      }
      if ($var = isset($_ENV['windir']) ? $_ENV['windir'] : getenv('windir'))
      {
        return $var;
      }

      return getenv('SystemRoot').'\temp';
    }

    if ($var = isset($_ENV['TMPDIR']) ? $_ENV['TMPDIR'] : getenv('TMPDIR'))
    {
      return $var;
    }

    return '/tmp';
  }
}
