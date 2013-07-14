<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

pake_desc('initialize a new symfony project');
pake_task('init-project');
pake_alias('new', 'init-project');

pake_desc('initialize a new symfony application');
pake_task('init-app', 'project_exists');
pake_alias('app', 'init-app');

pake_desc('initialize a new symfony module');
pake_task('init-module', 'app_exists');
pake_alias('module', 'init-module');

pake_desc('initialize a new symfony batch script');
pake_task('init-batch', 'project_exists');
pake_alias('batch', 'init-batch');

pake_desc('initialize a new symfony controller script');
pake_task('init-controller', 'app_exists');
pake_alias('controller', 'init-controller');

function run_init_project($task, $args)
{
  if (file_exists('symfony'))
  {
    throw new Exception('A symfony project already exists in this directory.');
  }

  if (!count($args))
  {
    throw new Exception('You must provide a project name.');
  }

  $project_name = $args[0];

  $sf_root_dir = sfConfig::get('sf_root_dir');

  // create basic project structure
  $finder = pakeFinder::type('any')->ignore_version_control()->discard('.sf');
  pake_mirror($finder, sfConfig::get('sf_symfony_data_dir').'/skeleton/project', $sf_root_dir);

  $finder = pakeFinder::type('file')->name('properties.ini', 'apache.conf', 'propel.ini');
  pake_replace_tokens($finder, $sf_root_dir, '##', '##', array('PROJECT_NAME' => $project_name));

  $finder = pakeFinder::type('file')->name('propel.ini');
  pake_replace_tokens($finder, $sf_root_dir, '##', '##', array('PROJECT_DIR' => $sf_root_dir));

  // update config/config.php
  pake_replace_tokens('config.php', sfConfig::get('sf_config_dir'), '##', '##', array(
    'SYMFONY_LIB_DIR'  => sfConfig::get('sf_symfony_lib_dir'),
    'SYMFONY_DATA_DIR' => sfConfig::get('sf_symfony_data_dir'),
  ));

  run_fix_perms($task, $args);
}

function run_init_app($task, $args)
{
  if (!count($args))
  {
    throw new Exception('You must provide your application name.');
  }

  $app = $args[0];

  $sf_root_dir = sfConfig::get('sf_root_dir');
  $app_dir = $sf_root_dir.'/'.sfConfig::get('sf_apps_dir_name').'/'.$app;

  if (is_dir($app_dir))
  {
    throw new Exception(sprintf('The directory "%s" already exists.', $app_dir));
  }

  // create basic application structure
  $finder = pakeFinder::type('any')->ignore_version_control()->discard('.sf');
  pake_mirror($finder, sfConfig::get('sf_symfony_data_dir').'/skeleton/app/app', $app_dir);

  // create $app.php or index.php if it is our first app
  $index_name = 'index';
  $first_app = file_exists(sfConfig::get('sf_web_dir').'/index.php') ? false : true;
  if (!$first_app)
  {
    $index_name = $app;
  }

  // set no_script_name value in settings.yml for production environment
  $finder = pakeFinder::type('file')->name('settings.yml');
  pake_replace_tokens($finder, $app_dir.'/'.sfConfig::get('sf_app_config_dir_name'), '##', '##', array('NO_SCRIPT_NAME' => ($first_app ? 'on' : 'off')));

  pake_copy(sfConfig::get('sf_symfony_data_dir').'/skeleton/app/web/index.php', sfConfig::get('sf_web_dir').'/'.$index_name.'.php');
  pake_copy(sfConfig::get('sf_symfony_data_dir').'/skeleton/app/web/index_dev.php', sfConfig::get('sf_web_dir').'/'.$app.'_dev.php');

  $finder = pakeFinder::type('file')->name($index_name.'.php', $app.'_dev.php');
  pake_replace_tokens($finder, sfConfig::get('sf_web_dir'), '##', '##', array('APP_NAME' => $app));

  run_fix_perms($task, $args);

  // create test dir
  pake_mkdirs($sf_root_dir.'/test/functional/'.$app);
}

function run_init_module($task, $args)
{
  if (count($args) < 2)
  {
    throw new Exception('You must provide your module name.');
  }

  $app    = $args[0];
  $module = $args[1];
  $sf_root_dir = sfConfig::get('sf_root_dir');
  $module_dir  = $sf_root_dir.'/'.sfConfig::get('sf_apps_dir_name').'/'.$app.'/'.sfConfig::get('sf_app_module_dir_name').'/'.$module;

  if (is_dir($module_dir))
  {
    throw new Exception(sprintf('The directory "%s" already exists.', $module_dir));
  }

  try
  {
    $author_name = $task->get_property('author', 'symfony');
  }
  catch (pakeException $e)
  {
    $author_name = 'Your name here';
  }

  $constants = array(
    'PROJECT_NAME' => $task->get_property('name', 'symfony'),
    'APP_NAME'     => $app,
    'MODULE_NAME'  => $module,
    'AUTHOR_NAME'  => $author_name,
  );

  if (is_readable(sfConfig::get('sf_data_dir').'/skeleton/module'))
  {
    $sf_skeleton_dir = sfConfig::get('sf_data_dir').'/skeleton/module';
  }
  else
  {
    $sf_skeleton_dir = sfConfig::get('sf_symfony_data_dir').'/skeleton/module';
  }

  // create basic application structure
  $finder = pakeFinder::type('any')->ignore_version_control()->discard('.sf');
  pake_mirror($finder, $sf_skeleton_dir.'/module', $module_dir);

  // create basic test
  pake_copy($sf_skeleton_dir.'/test/actionsTest.php', $sf_root_dir.'/test/functional/'.$app.'/'.$module.'ActionsTest.php');

  // customize test file
  pake_replace_tokens($module.'ActionsTest.php', $sf_root_dir.'/test/functional/'.$app, '##', '##', $constants);

  // customize php and yml files
  $finder = pakeFinder::type('file')->name('*.php', '*.yml');
  pake_replace_tokens($finder, $module_dir, '##', '##', $constants);
}

function run_init_batch($task, $args)
{
  // handling two required arguments (application and batch name)
  if (count($args) < 1)
  {
    throw new Exception('You must provide the batch skeleton name');
  }

  // TODO: add finder here to locate batch skeleton locally or in symfony dirs, and send path to skeletons function
  $batch = '_batch_'.$args[0];

  if (!function_exists($batch))
  {
    throw new Exception(sprintf('The specified batch "%s" does not exist.', $args[0]));
  }

  $batch($task, $args);

  if (!file_exists(sfConfig::get('sf_symfony_data_dir').'/skeleton/batch/'.$args[0].'.php'))
  {
    throw new Exception('The skeleton you specified could not be found.');
  }
}

function _batch_default($task, $args)
{
  if (count($args) < 2)
  {
    throw new Exception('You must provide the destination script name');
  }
  if (count($args) < 3)
  {
    throw new Exception('You must provide the application name');
  }

  $batch = $args[1];
  $app   = $args[2];

  // handling two optional arguments (environment and debug)
  $env   = isset($args[3]) ? $args[3] : 'dev';
  $debug = isset($args[4]) ? $args[4] : true;

  $constants = array(
    'PROJECT_NAME' => $task->get_property('name', 'symfony'),
    'APP_NAME'     => $app,
    'BATCH_NAME'   => $batch,
    'ENV_NAME'     => $env,
    'DEBUG'        => (boolean) $debug,
  );

  $sf_bin_dir = sfConfig::get('sf_bin_dir');

  pake_copy(sfConfig::get('sf_symfony_data_dir').'/skeleton/batch/default.php', $sf_bin_dir.'/'.$batch.'.php');
  pake_replace_tokens($batch.'.php', $sf_bin_dir, '##', '##', $constants);
}

function _batch_rotate_log($task, $args)
{
  if (count($args) < 2)
  {
    throw new Exception('You must provide the application');
  }
  if (count($args) < 3)
  {
    throw new Exception('You must provide the environment');
  }

  $app = $args[1];
  $env = $args[2];
  $batch = 'rotate_log_'.$app.'_'.$env;

  // handling two optional arguments (environment and debug)
  $env   = isset($args[3]) ? $args[3] : 'dev';
  $debug = isset($args[4]) ? $args[4] : true;

  $constants = array(
    'PROJECT_NAME' => $task->get_property('name', 'symfony'),
    'APP_NAME'     => $app,
    'BATCH_NAME'   => $batch,
    'ENV_NAME'     => $env,
    'DEBUG'        => (boolean) $debug,
  );

  $sf_bin_dir = sfConfig::get('sf_bin_dir');

  pake_copy(sfConfig::get('sf_symfony_data_dir').'/skeleton/batch/rotate_log.php', $sf_bin_dir.'/'.$batch.'.php');
  pake_replace_tokens($batch.'.php', $sf_bin_dir, '##', '##', $constants);
}

function run_init_controller($task, $args)
{
  // handling two required arguments (application and batch name)
  if (count($args) < 2)
  {
    throw new Exception('You must provide the environment name');
  }

  $app = $args[0];
  $env = $args[1];

  // handling two optional arguments (environment and debug)
  $controller   = isset($args[2]) ? $args[2] : $app.'_'.$env;
  $debug        = isset($args[3]) ? $args[3] : true;

  $constants = array(
    'PROJECT_NAME'    => $task->get_property('name', 'symfony'),
    'APP_NAME'        => $app,
    'CONTROLLER_NAME' => $controller,
    'ENV_NAME'        => $env,
    'DEBUG'           => (boolean) $debug,
  );

  $sf_web_dir = sfConfig::get('sf_web_dir');

  pake_copy(sfConfig::get('sf_symfony_data_dir').'/skeleton/controller/controller.php', $sf_web_dir.'/'.$controller.'.php');
  pake_replace_tokens($controller.'.php', $sf_web_dir, '##', '##', $constants);
}
