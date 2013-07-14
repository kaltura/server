<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

pake_task('project_exists');
pake_task('app_exists', 'project_exists');
pake_task('module_exists', 'app_exists');

function run_project_exists($task, $args)
{
  if (!file_exists('symfony'))
  {
    throw new Exception('you must be in a symfony project directory');
  }

  pake_properties('config/properties.ini');
}

function run_app_exists($task, $args)
{
  if (!count($args))
  {
    throw new Exception('you must provide your application name');
  }

  if (!is_dir(getcwd().'/apps/'.$args[0]))
  {
    throw new Exception('application "'.$args[0].'" does not exist');
  }
}

function run_module_exists($task, $args)
{
  if (count($args) < 2)
  {
    throw new Exception('you must provide your module name');
  }

  if (!is_dir(getcwd().'/apps/'.$args[0].'/modules/'.$args[1]))
  {
    throw new Exception('module "'.$args[1].'" does not exist');
  }
}
