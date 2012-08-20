<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

pake_desc('clear cached information');
pake_task('clear-cache', 'project_exists');
pake_alias('cc', 'clear-cache');

pake_desc('clear controllers');
pake_task('clear-controllers', 'project_exists');

pake_desc('fix directories permissions');
pake_task('fix-perms', 'project_exists');

pake_desc('rotates an applications log files');
pake_task('log-rotate', 'app_exists');

pake_desc('purges an applications log files');
pake_task('log-purge', 'project_exists');

pake_desc('enables an application in a given environment');
pake_task('enable', 'app_exists');

pake_desc('disables an application in a given environment');
pake_task('disable', 'app_exists');

/**
 * fixes permissions in a symfony project
 *
 * @example symfony fix-perms
 *
 * @param object $task
 * @param array $args
 */
function run_fix_perms($task, $args)
{
  $sf_root_dir = sfConfig::get('sf_root_dir');

  pake_chmod(sfConfig::get('sf_cache_dir_name'), $sf_root_dir, 0777);
  pake_chmod(sfConfig::get('sf_log_dir_name'), $sf_root_dir, 0777);
  pake_chmod(sfConfig::get('sf_web_dir_name').DIRECTORY_SEPARATOR.sfConfig::get('sf_upload_dir_name'), $sf_root_dir, 0777);
  pake_chmod('symfony', $sf_root_dir, 0777);

  $dirs = array(sfConfig::get('sf_cache_dir_name'), sfConfig::get('sf_web_dir_name').DIRECTORY_SEPARATOR.sfConfig::get('sf_upload_dir_name'), sfConfig::get('sf_log_dir_name'));
  $dir_finder = pakeFinder::type('dir')->ignore_version_control();
  $file_finder = pakeFinder::type('file')->ignore_version_control();
  foreach ($dirs as $dir)
  {
    pake_chmod($dir_finder, $dir, 0777);
    pake_chmod($file_finder, $dir, 0666);
  }
}

/**
 * clears symfony project cache
 *
 * @example symfony clear-cache
 * @example symfony cc
 *
 * @param object $task
 * @param array $args
 */
function run_clear_cache($task, $args)
{
  if (!file_exists('cache'))
  {
    throw new Exception('Cache directory does not exist.');
  }

  $cache_dir = sfConfig::get('sf_cache_dir_name');

  // app
  $main_app = '';
  if (isset($args[0]))
  {
    $main_app = $args[0];
  }

  // type (template, i18n or config)
  $main_type = '';
  if (isset($args[1]))
  {
    $main_type = $args[1];
  }

  // declare type that must be cleaned safely (with a lock file during cleaning)
  $safe_types = array(sfConfig::get('sf_app_config_dir_name'), sfConfig::get('sf_app_i18n_dir_name'));

  // finder to remove all files in a cache directory
  $finder = pakeFinder::type('file')->ignore_version_control()->discard('.sf');

  // finder to find directories (1 level) in a directory
  $dir_finder = pakeFinder::type('dir')->ignore_version_control()->discard('.sf')->maxdepth(0)->relative();

  // iterate through applications
  $apps = array();
  if ($main_app)
  {
    $apps[] = $main_app;
  }
  else
  {
    $apps = $dir_finder->in($cache_dir);
  }

  foreach ($apps as $app)
  {
    if (!is_dir($cache_dir.'/'.$app))
    {
      continue;
    }

    // remove cache for all environments
    foreach ($dir_finder->in($cache_dir.'/'.$app) as $env)
    {
      // which types?
      $types = array();
      if ($main_type)
      {
        $types[] = $main_type;
      }
      else
      {
        $types = $dir_finder->in($cache_dir.'/'.$app.'/'.$env);
      }

      $sf_root_dir = sfConfig::get('sf_root_dir');
      foreach ($types as $type)
      {
        $sub_dir = $cache_dir.'/'.$app.'/'.$env.'/'.$type;

        if (!is_dir($sub_dir))
        {
          continue;
        }

        // remove cache files
        if (in_array($type, $safe_types))
        {
          $lock_name = $app.'_'.$env;
          _safe_cache_remove($finder, $sub_dir, $lock_name);
        }
        else
        {
          pake_remove($finder, $sf_root_dir.'/'.$sub_dir);
        }
      }
    }
  }
}

/**
 * clears all controllers in your web directory other than one running in a produciton environment
 *
 * @example symfony clear-controllers
 *
 * @param object $task
 * @param array $args
 */
function run_clear_controllers($task, $args)
{
  $web_dir = sfConfig::get('sf_web_dir');
  $app_dir = sfConfig::get('sf_app_dir');

  $apps = count($args) > 1 ? $args : null;

  // get controller
  $controllers = pakeFinder::type('file')->ignore_version_control()->maxdepth(1)->name('*.php')->in($web_dir);

  foreach ($controllers as $controller)
  {
    $contents = file_get_contents($controller);
    preg_match('/\'SF_APP\',[\s]*\'(.*)\'\)/', $contents, $found_app);
    preg_match('/\'SF_ENVIRONMENT\',[\s]*\'(.*)\'\)/', $contents, $env);

    // remove file if it has found an application and the environment is not production
    if (isset($found_app[1]) && isset($env[1]) && $env[1] != 'prod')
    {
      pake_remove($controller, '');
    }
  }
}

/**
 * safely removes directory via pake
 *
 * @param object $finder
 * @param string $sub_dir
 * @param string $lock_name
 */
function _safe_cache_remove($finder, $sub_dir, $lock_name)
{
  $sf_root_dir = sfConfig::get('sf_root_dir');

  // create a lock file
  pake_touch($sf_root_dir.'/'.$lock_name.'.lck', '');

  // change mode so the web user can remove it if we die
  pake_chmod($lock_name.'.lck', $sf_root_dir, 0777);

  // remove cache files
  pake_remove($finder, $sf_root_dir.'/'.$sub_dir);

  // release lock
  pake_remove($sf_root_dir.'/'.$lock_name.'.lck', '');
}

/**
 * forces rotation of the given log file
 *
 * @example symfony log-rotate
 *
 * @param object $task
 * @param array $args
 */
function run_log_rotate($task, $args)
{
  // handling two required arguments (application and environment)
  if (count($args) < 2)
  {
    throw new Exception('You must provide the environment of the log to rotate');
  }
  $app = $args[0];
  $env = $args[1];

  // define constants
  define('SF_ROOT_DIR',    sfConfig::get('sf_root_dir'));
  define('SF_APP',         $app);
  define('SF_ENVIRONMENT', $env);
  define('SF_DEBUG',       true);

  // get configuration
  require_once SF_ROOT_DIR.DIRECTORY_SEPARATOR.'apps'.DIRECTORY_SEPARATOR.SF_APP.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'config.php';

  sfLogManager::rotate($app, $env, sfConfig::get('sf_logging_period'), sfConfig::get('sf_logging_history'), true);
}

/**
 * purges the application log directory as per settings in logging.yml
 *
 * @example symfony log-purge
 *
 * @param object $task
 * @param array $args
 */
function run_log_purge($task, $args)
{
  $sf_symfony_data_dir = sfConfig::get('sf_symfony_data_dir');

  $default_logging = sfYaml::load($sf_symfony_data_dir.'/config/logging.yml');
  $app_dir = sfConfig::get('sf_app_dir');
  $apps = pakeFinder::type('dir')->maxdepth(0)->relative()->ignore_version_control()->in('apps');
  $ignore = array('all', 'default');

  foreach ($apps as $app)
  {
    $logging = sfYaml::load($app_dir.'/'.$app.'/config/logging.yml');
    $logging = array_merge($default_logging, $logging);

    foreach ($logging as $env => $config)
    {
      if (in_array($env, $ignore))
      {
        continue;
      }
      $props = array_merge($default_logging['default'], is_array($config) ? $config : array());
      $active = isset($props['active']) ? $props['active'] : true;
      $purge  = isset($props['purge']) ? $props['purge'] : true;
      if ($active && $purge)
      {
        $filename = sfConfig::get('sf_log_dir').'/'.$app.'_'.$env.'.log';
        if (file_exists($filename))
        {
          pake_remove($filename, '');
        }
      }
    }
  }
}

function run_enable($task, $args)
{
  // handling two required arguments (application and environment)
  if (count($args) < 2)
  {
    throw new Exception('You must provide an environment for the application.');
  }

  $app = $args[0];
  $env = $args[1];

  $lockFile = $app.'_'.$env.'.clilock';
  $locks = pakeFinder::type('file')->prune('.svn')->discard('.svn')->maxdepth(0)->name($lockFile)->relative()->in('./');

  if (file_exists(sfConfig::get('sf_root_dir').'/'.$lockFile))
  {
    pake_remove($lockFile, '');
    run_clear_cache($task, array()); 
    pake_echo_action('enable', "$app [$env] has been ENABLED");

    return;
  }

  pake_echo_action('enable', "$app [$env] is currently ENABLED");
}

function run_disable($task, $args)
{
  // handling two required arguments (application and environment)
  if (count($args) < 2)
  {
    throw new Exception('You must provide an environment for the application.');
  }

  $app = $args[0];
  $env = $args[1];

  $lockFile = $app.'_'.$env.'.clilock';

  if (!file_exists(sfConfig::get('sf_root_dir').'/'.$lockFile))
  {
    pake_touch(sfConfig::get('sf_root_dir').'/'.$lockFile, '777');

    pake_echo_action('enable', "$app [$env] has been DISABLED");

    return;
  }

  pake_echo_action('enable', "$app [$env] is currently DISABLED");

  return;
}
