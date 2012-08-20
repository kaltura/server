<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

pake_desc('synchronise project with another machine');
pake_task('sync', 'project_exists');

function run_sync($task, $args)
{
  if (!count($args))
  {
    throw new Exception('You must provide an environment to synchronize.');
  }

  $env = $args[0];

  $dryrun = isset($args[1]) ? $args[1] : false;

  if (!file_exists('config/rsync_exclude.txt'))
  {
    throw new Exception('You must create a rsync_exclude file for your project.');
  }

  $host = $task->get_property('host', $env);
  $dir  = $task->get_property('dir', $env);
  try
  {
    $user = $task->get_property('user', $env).'@';
  }
  catch (pakeException $e)
  {
    $user = '';
  }

  if (substr($dir, -1) != '/')
  {
    $dir .= '/';
  }

  $ssh = 'ssh';

  try
  {
    $port = $task->get_property('port', $env);
    $ssh = '"ssh -p'.$port.'"';
  }
  catch (pakeException $e) {}

  try
  {
    $parameters = $task->get_property('parameters', $env);
  }
  catch (pakeException $e)
  {
    $parameters = '-azC --force --delete';
    if (file_exists('config/rsync_exclude.txt'))
    {
      $parameters .= ' --exclude-from=config/rsync_exclude.txt';
    }

    if (file_exists('config/rsync_include.txt'))
    {
      $parameters .= ' --include-from=config/rsync_include.txt';
    }

    if (file_exists('config/rsync.txt'))
    {
      $parameters .= ' --files-from=config/rsync.txt';
    }
  }

  $dry_run = ($dryrun == 'go' || $dryrun == 'ok') ? '' : '--dry-run';
  $cmd = "rsync --progress $dry_run $parameters -e $ssh ./ $user$host:$dir";

  echo pake_sh($cmd);
}
