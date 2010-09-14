<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

pake_desc('initialize a new propel CRUD module');
pake_task('propel-init-crud', 'app_exists');

pake_desc('generate a new propel CRUD module');
pake_task('propel-generate-crud', 'app_exists');

function run_propel_init_crud($task, $args)
{
  if (count($args) < 2)
  {
    throw new Exception('You must provide your module name.');
  }

  if (count($args) < 3)
  {
    throw new Exception('You must provide your model class name.');
  }

  $app         = $args[0];
  $module      = $args[1];
  $model_class = $args[2];

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
    'MODEL_CLASS'  => $model_class,
    'AUTHOR_NAME'  => $author_name,
  );

  $sf_root_dir = sfConfig::get('sf_root_dir');
  $moduleDir   = $sf_root_dir.'/'.sfConfig::get('sf_apps_dir_name').'/'.$app.'/'.sfConfig::get('sf_app_module_dir_name').'/'.$module;

  // create basic application structure
  $finder = pakeFinder::type('any')->ignore_version_control()->discard('.sf');
  pake_mirror($finder, sfConfig::get('sf_symfony_data_dir').'/generator/sfPropelCrud/default/skeleton', $moduleDir);

  // create basic test
  pake_copy(sfConfig::get('sf_symfony_data_dir').'/skeleton/module/test/actionsTest.php', $sf_root_dir.'/test/functional/'.$app.'/'.$module.'ActionsTest.php');

  // customize test file
  pake_replace_tokens($module.'ActionsTest.php', $sf_root_dir.'/test/functional/'.$app, '##', '##', $constants);

  // customize php and yml files
  $finder = pakeFinder::type('file')->name('*.php', '*.yml');
  pake_replace_tokens($finder, $moduleDir, '##', '##', $constants);
}

function run_propel_generate_crud($task, $args)
{
  if (count($args) < 2)
  {
    throw new Exception('You must provide your module name.');
  }

  if (count($args) < 3)
  {
    throw new Exception('You must provide your model class name.');
  }

  $theme = isset($args[3]) ? $args[3] : 'default';

  $app         = $args[0];
  $module      = $args[1];
  $model_class = $args[2];

  $sf_root_dir = sfConfig::get('sf_root_dir');

  // generate module
  $tmp_dir = $sf_root_dir.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'tmp'.DIRECTORY_SEPARATOR.md5(uniqid(rand(), true));
  sfConfig::set('sf_module_cache_dir', $tmp_dir);
  $generator_manager = new sfGeneratorManager();
  $generator_manager->initialize();
  $generator_manager->generate('sfPropelCrudGenerator', array('model_class' => $model_class, 'moduleName' => $module, 'theme' => $theme));

  $moduleDir = $sf_root_dir.'/'.sfConfig::get('sf_apps_dir_name').'/'.$app.'/'.sfConfig::get('sf_app_module_dir_name').'/'.$module;

  // copy our generated module
  $finder = pakeFinder::type('any');
  pake_mirror($finder, $tmp_dir.'/auto'.ucfirst($module), $moduleDir);

  // change module name
  pake_replace_tokens($moduleDir.'/actions/actions.class.php', getcwd(), '', '', array('auto'.ucfirst($module) => $module));

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
    'MODEL_CLASS'  => $model_class,
    'AUTHOR_NAME'  => $author_name,
  );

  // customize php and yml files
  $finder = pakeFinder::type('file')->name('*.php', '*.yml');
  pake_replace_tokens($finder, $moduleDir, '##', '##', $constants);

  // create basic test
  pake_copy(sfConfig::get('sf_symfony_data_dir').'/skeleton/module/test/actionsTest.php', $sf_root_dir.'/test/functional/'.$app.'/'.$module.'ActionsTest.php');

  // customize test file
  pake_replace_tokens($module.'ActionsTest.php', $sf_root_dir.'/test/functional/'.$app, '##', '##', $constants);

  // delete temp files
  $finder = pakeFinder::type('any');
  pake_remove($finder, $tmp_dir);
}
