<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * core symfony class.
 *
 * @package    symfony
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfCore.class.php 3128 2007-01-03 08:01:46Z fabien $
 */
class sfCore
{
  static protected
    $autoloadCallables = array(),
    $classes           = array();

  static public function bootstrap($sf_symfony_lib_dir, $sf_symfony_data_dir)
  {
    require_once($sf_symfony_lib_dir.'/util/sfToolkit.class.php');
    require_once($sf_symfony_lib_dir.'/config/sfConfig.class.php');

    sfCore::initConfiguration($sf_symfony_lib_dir, $sf_symfony_data_dir);
    if (sfConfig::get('sf_check_lock'))
    {
      sfCore::checkLock();
    }
    if (sfConfig::get('sf_check_symfony_version'))
    {
      sfCore::checkSymfonyVersion();
    }
    sfCore::initIncludePath();

    sfCore::callBootstrap();
  }

  static public function callBootstrap()
  {
    $bootstrap = sfConfig::get('sf_config_cache_dir').'/config_bootstrap_compile.yml.php';
    if (is_readable($bootstrap))
    {
      sfConfig::set('sf_in_bootstrap', true);
      require($bootstrap);
    }
    else
    {
      require(sfConfig::get('sf_symfony_lib_dir').'/symfony.php');
    }
  }

  static public function initConfiguration($sf_symfony_lib_dir, $sf_symfony_data_dir, $test = false)
  {
    // start timer
    if (SF_DEBUG)
    {
      sfConfig::set('sf_timer_start', microtime(true));
    }

    // main configuration
    sfConfig::add(array(
      'sf_root_dir'         => SF_ROOT_DIR,
      'sf_app'              => SF_APP,
      'sf_environment'      => SF_ENVIRONMENT,
      'sf_debug'            => SF_DEBUG,
      'sf_symfony_lib_dir'  => $sf_symfony_lib_dir,
      'sf_symfony_data_dir' => $sf_symfony_data_dir,
      'sf_test'             => $test,
    ));

    // directory layout
    include($sf_symfony_data_dir.'/config/constants.php');
  }

  static public function initIncludePath()
  {
    set_include_path(
      sfConfig::get('sf_lib_dir').PATH_SEPARATOR.
      sfConfig::get('sf_root_dir').PATH_SEPARATOR.
      sfConfig::get('sf_app_lib_dir').PATH_SEPARATOR.
      sfConfig::get('sf_symfony_lib_dir').DIRECTORY_SEPARATOR.'vendor'.PATH_SEPARATOR.
      get_include_path()
    );
  }

  // check to see if we're not in a cache cleaning process
  static public function checkLock()
  {
    if (sfToolkit::hasLockFile(SF_ROOT_DIR.DIRECTORY_SEPARATOR.SF_APP.'_'.SF_ENVIRONMENT.'.lck', 5))
    {
      // application is not available
      $file = sfConfig::get('sf_web_dir').'/errors/unavailable.php';
      include(is_readable($file) ? $file : sfConfig::get('sf_symfony_data_dir').'/web/errors/unavailable.php');

      die(1);
    }
  }

  static public function checkSymfonyVersion()
  {
    // recent symfony update?
    $last_version    = @file_get_contents(sfConfig::get('sf_config_cache_dir').'/VERSION');
    $current_version = trim(file_get_contents(sfConfig::get('sf_symfony_lib_dir').'/VERSION'));
    if ($last_version != $current_version)
    {
      // clear cache
      sfToolkit::clearDirectory(sfConfig::get('sf_config_cache_dir'));
    }
  }

  static public function getClassPath($class)
  {
    return isset(self::$classes[$class]) ? self::$classes[$class] : null;
  }

  static public function addAutoloadCallable($callable)
  {
    self::$autoloadCallables[] = $callable;

    if (function_exists('spl_autoload_register'))
    {
      spl_autoload_register($callable);
    }
  }

  static public function getAutoloadCallables()
  {
    return self::$autoloadCallables;
  }

  /**
   * Handles autoloading of classes that have been specified in autoload.yml.
   *
   * @param  string  A class name.
   *
   * @return boolean Returns true if the class has been loaded
   */
  static public function splAutoload($class)
  {
    // load the list of autoload classes
    if (!self::$classes)
    {
      $file = sfConfigCache::getInstance()->checkConfig(sfConfig::get('sf_app_config_dir_name').'/autoload.yml');
      self::$classes = include($file);
    }

    // class already exists
    if (class_exists($class, false))
    {
      return true;
    }

    // we have a class path, let's include it
    if (isset(self::$classes[$class]))
    {
      require(self::$classes[$class]);

      return true;
    }

    // see if the file exists in the current module lib directory
    // must be in a module context
    if (sfContext::hasInstance() && ($module = sfContext::getInstance()->getModuleName()) && isset(self::$classes[$module.'/'.$class]))
    {
      require(self::$classes[$module.'/'.$class]);

      return true;
    }

    return false;
  }

  static public function initAutoload()
  {
    if (function_exists('spl_autoload_register'))
    {
      ini_set('unserialize_callback_func', 'spl_autoload_call');
    }
    else if (!function_exists('__autoload'))
    {
      ini_set('unserialize_callback_func', '__autoload');

      function __autoload($class)
      {
        foreach (sfCore::getAutoloadCallables() as $callable)
        {
          if (call_user_func($callable, $class))
          {
            return true;
          }
        }

        // unspecified class
        // do not print an error if the autoload came from class_exists
        $trace = debug_backtrace();
        if (count($trace) < 1 || ($trace[1]['function'] != 'class_exists' && $trace[1]['function'] != 'is_a'))
        {
          $error = sprintf('Autoloading of class "%s" failed. Try to clear the symfony cache and refresh. [err0003]', $class);
          $e = new sfAutoloadException($error);

          $e->printStackTrace();
        }
      }
    }

    self::addAutoloadCallable(array('sfCore', 'splAutoload'));
  }

  static public function splSimpleAutoload($class)
  {
    // class already exists
    if (class_exists($class, false))
    {
      return true;
    }

    // we have a class path, let's include it
    if (isset(self::$classes[$class]))
    {
      require(self::$classes[$class]);

      return true;
    }

    return false;
  }

  static public function initSimpleAutoload($dirs)
  {
    require_once(dirname(__FILE__).'/sfFinder.class.php');
    self::$classes = array();
    $finder = sfFinder::type('file')->ignore_version_control()->name('*.php');
    foreach ((array) $dirs as $dir)
    {
      $files = $finder->in(glob($dir));
      if (is_array($files))
      {
        foreach ($files as $file)
        {
          preg_match_all('~^\s*(?:abstract\s+|final\s+)?(?:class|interface)\s+(\w+)~mi', file_get_contents($file), $classes);
          foreach ($classes[1] as $class)
          {
            self::$classes[$class] = $file;
          }
        }
      }
    }

    if (function_exists('spl_autoload_register'))
    {
      ini_set('unserialize_callback_func', 'spl_autoload_call');

      spl_autoload_register(array('sfCore', 'splSimpleAutoload'));
    }
    elseif (!function_exists('__autoload'))
    {
      ini_set('unserialize_callback_func', '__autoload');

      function __autoload($class)
      {
        return sfCore::splSimpleAutoload($class);
      }
    }
  }
}
