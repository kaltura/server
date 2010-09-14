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
 * Pre-initialization script.
 *
 * @package    symfony
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @author     Sean Kerr <skerr@mojavi.org>
 * @version    SVN: $Id: symfony.php 3285 2007-01-15 20:01:09Z fabien $
 */

$sf_symfony_lib_dir = sfConfig::get('sf_symfony_lib_dir');
if (!sfConfig::get('sf_in_bootstrap'))
{
  // YAML support
  require_once($sf_symfony_lib_dir.'/util/sfYaml.class.php');

  // cache support
  require_once($sf_symfony_lib_dir.'/cache/sfCache.class.php');
  require_once($sf_symfony_lib_dir.'/cache/sfFileCache.class.php');

  // config support
  require_once($sf_symfony_lib_dir.'/config/sfConfigCache.class.php');
  require_once($sf_symfony_lib_dir.'/config/sfConfigHandler.class.php');
  require_once($sf_symfony_lib_dir.'/config/sfYamlConfigHandler.class.php');
  require_once($sf_symfony_lib_dir.'/config/sfAutoloadConfigHandler.class.php');
  require_once($sf_symfony_lib_dir.'/config/sfRootConfigHandler.class.php');
  require_once($sf_symfony_lib_dir.'/config/sfLoader.class.php');

  // basic exception classes
  require_once($sf_symfony_lib_dir.'/exception/sfException.class.php');
  require_once($sf_symfony_lib_dir.'/exception/sfAutoloadException.class.php');
  require_once($sf_symfony_lib_dir.'/exception/sfCacheException.class.php');
  require_once($sf_symfony_lib_dir.'/exception/sfConfigurationException.class.php');
  require_once($sf_symfony_lib_dir.'/exception/sfParseException.class.php');

  // utils
  require_once($sf_symfony_lib_dir.'/util/sfParameterHolder.class.php');
}
else
{
  require_once($sf_symfony_lib_dir.'/config/sfConfigCache.class.php');
}

// autoloading
sfCore::initAutoload();

try
{
  $configCache = sfConfigCache::getInstance();

  // force setting default timezone if not set
  if (function_exists('date_default_timezone_get'))
  {
    if ($default_timezone = sfConfig::get('sf_default_timezone'))
    {
      date_default_timezone_set($default_timezone);
    }
    else if (sfConfig::get('sf_force_default_timezone', true))
    {
      date_default_timezone_set(@date_default_timezone_get());
    }
  }

  // get config instance
  $sf_app_config_dir_name = sfConfig::get('sf_app_config_dir_name');

  $sf_debug = sfConfig::get('sf_debug');

  // load timer classes if in debug mode
  if ($sf_debug)
  {
    require_once($sf_symfony_lib_dir.'/debug/sfTimerManager.class.php');
    require_once($sf_symfony_lib_dir.'/debug/sfTimer.class.php');
  }

  // load base settings
  include($configCache->checkConfig($sf_app_config_dir_name.'/settings.yml'));
  if (sfConfig::get('sf_logging_enabled', true))
  {
    include($configCache->checkConfig($sf_app_config_dir_name.'/logging.yml'));
  }
  if ($file = $configCache->checkConfig($sf_app_config_dir_name.'/app.yml', true))
  {
    include($file);
  }
  if (sfConfig::get('sf_i18n'))
  {
    include($configCache->checkConfig($sf_app_config_dir_name.'/i18n.yml'));
  }

  // add autoloading callables
  foreach ((array) sfConfig::get('sf_autoloading_functions', array()) as $callable)
  {
    sfCore::addAutoloadCallable($callable);
  }

  // error settings
  ini_set('display_errors', $sf_debug ? 'on' : 'off');
  error_reporting(sfConfig::get('sf_error_reporting'));

  // create bootstrap file for next time
  if (!sfConfig::get('sf_in_bootstrap') && !$sf_debug && !sfConfig::get('sf_test'))
  {
    $configCache->checkConfig($sf_app_config_dir_name.'/bootstrap_compile.yml');
  }

  // required core classes for the framework
  // create a temp var to avoid substitution during compilation
  if (!$sf_debug && !sfConfig::get('sf_test'))
  {
    $core_classes = $sf_app_config_dir_name.'/core_compile.yml';
    $configCache->import($core_classes, false);
  }

  $configCache->import($sf_app_config_dir_name.'/php.yml', false);
  $configCache->import($sf_app_config_dir_name.'/routing.yml', false);

  // include all config.php from plugins
  sfLoader::loadPluginConfig();

  // compress output
  ob_start(sfConfig::get('sf_compressed') ? 'ob_gzhandler' : '');
}
catch (sfException $e)
{
  $e->printStackTrace();
}
catch (Exception $e)
{
  if (sfConfig::get('sf_test'))
  {
    throw $e;
  }

  try
  {
    // wrap non symfony exceptions
    $sfException = new sfException();
    $sfException->printStackTrace($e);
  }
  catch (Exception $e)
  {
    header('HTTP/1.0 500 Internal Server Error');
  }
}
