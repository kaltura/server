<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

if (!isset($sf_symfony_lib_dir))
{
  die("You must launch symfony command line with the symfony script\n");
}

if (ini_get('zend.ze1_compatibility_mode'))
{
  die("symfony cannot run with zend.ze1_compatibility_mode enabled.\nPlease turn zend.ze1_compatibility_mode to Off in your php.ini.\n");
}

// check if we are using an old project
if (file_exists('config/config.php') && !isset($sf_symfony_lib_dir))
{
  // allow only upgrading
  if (!in_array('upgrade', $argv))
  {
    echo "Please upgrade your project before launching any other symfony task\n";
    exit();
  }
}

require_once($sf_symfony_lib_dir.'/vendor/pake/pakeFunction.php');
require_once($sf_symfony_lib_dir.'/vendor/pake/pakeGetopt.class.php');

// autoloading for pake tasks
class simpleAutoloader
{
  static public
    $class_paths        = array(),
    $autoload_callables = array();

  static public function initialize($sf_symfony_lib_dir)
  {
    self::$class_paths = array();

    self::register($sf_symfony_lib_dir, '.class.php');
    self::register($sf_symfony_lib_dir.'/vendor/propel', '.php');
    self::register($sf_symfony_lib_dir.'/vendor/creole', '.php');
    self::register('lib/model', '.php');
    self::register('plugins', '.php');
  }

  static public function __autoload($class)
  {
    if (!isset(self::$class_paths[$class]))
    {
      foreach ((array) self::$autoload_callables as $callable)
      {
        if (call_user_func($callable, $class))
        {
          return true;
        }
      }

      return false;
    }

    require_once(self::$class_paths[$class]);

    return true;
  }

  static public function register($dir, $ext)
  {
    if (!is_dir($dir))
    {
      return;
    }

    foreach (pakeFinder::type('file')->name('*'.$ext)->ignore_version_control()->follow_link()->in($dir) as $file)
    {
      self::$class_paths[str_replace($ext, '', str_replace('.class', '', basename($file, $ext)))] = $file;
    }
  }

  static public function add($class, $file)
  {
    if (!is_file($file))
    {
      return;
    }

    self::$class_paths[$class] = $file;
  }

  static public function registerCallable($callable)
  {
    if (!is_callable($callable))
    {
      throw new Exception('Autoload callable does not exist');
    }

    self::$autoload_callables[] = $callable;
  }
}

function __autoload($class)
{
  static $initialized = false;

  if (!$initialized)
  {
    simpleAutoloader::initialize(sfConfig::get('sf_symfony_lib_dir'));
    $initialized = true;
  }

  return simpleAutoloader::__autoload($class);
}

// trap -V before pake
if (in_array('-V', $argv) || in_array('--version', $argv))
{
  printf("symfony version %s\n", pakeColor::colorize(trim(file_get_contents($sf_symfony_lib_dir.'/VERSION')), 'INFO'));
  exit(0);
}

if (count($argv) <= 1)
{
  $argv[] = '-T';
}

require_once($sf_symfony_lib_dir.'/config/sfConfig.class.php');

sfConfig::add(array(
  'sf_root_dir'         => getcwd(),
  'sf_symfony_lib_dir'  => $sf_symfony_lib_dir,
  'sf_symfony_data_dir' => $sf_symfony_data_dir,
));

// directory layout
include($sf_symfony_data_dir.'/config/constants.php');

// include path
set_include_path(
  sfConfig::get('sf_lib_dir').PATH_SEPARATOR.
  sfConfig::get('sf_app_lib_dir').PATH_SEPARATOR.
  sfConfig::get('sf_model_dir').PATH_SEPARATOR.
  sfConfig::get('sf_symfony_lib_dir').DIRECTORY_SEPARATOR.'vendor'.PATH_SEPARATOR.
  get_include_path()
);

// register tasks
$dirs = array(
  sfConfig::get('sf_data_dir').DIRECTORY_SEPARATOR.'tasks'         => 'myPake*.php', // project tasks
  sfConfig::get('sf_symfony_data_dir').DIRECTORY_SEPARATOR.'tasks' => 'sfPake*.php', // symfony tasks
  sfConfig::get('sf_root_dir').'/plugins/*/data/tasks'             => '*.php',       // plugin tasks
);
foreach ($dirs as $globDir => $name)
{
  if ($dirs = glob($globDir))
  {
    $tasks = pakeFinder::type('file')->name($name)->in($dirs);
    foreach ($tasks as $task)
    {
      include_once($task);
    }
  }
}

// run task
pakeApp::get_instance()->run(null, null, false);

exit(0);
