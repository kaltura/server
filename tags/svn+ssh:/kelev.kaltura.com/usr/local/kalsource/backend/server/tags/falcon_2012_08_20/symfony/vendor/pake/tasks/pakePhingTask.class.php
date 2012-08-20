<?php

/**
 * @package    pake
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @copyright  2004-2005 Fabien Potencier <fabien.potencier@symfony-project.com>
 * @license    see the LICENSE file included in the distribution
 * @version    SVN: $Id: pakePhingTask.class.php 1846 2006-08-25 12:35:26Z fabien $
 */

include_once 'phing/Phing.php';
if (!class_exists('Phing'))
{
  throw new pakeException('You must install Phing to use this task. (pear install http://phing.info/pear/phing-current.tgz)');
}

class pakePhingTask
{
  public static function import_default_tasks()
  {
  }

  public static function call_phing($task, $target, $build_file = '', $options = array())
  {
    $args = array();
    foreach ($options as $key => $value)
    {
      $args[] = "-D$key=$value";
    }

    if ($build_file)
    {
      $args[] = '-f';
      $args[] = realpath($build_file);
    }

    if (!$task->is_verbose())
    {
      $args[] = '-q';
    }

    if (is_array($target))
    {
      $args = array_merge($args, $target);
    }
    else
    {
      $args[] = $target;
    }

    Phing::startup();
    Phing::setProperty('phing.home', getenv('PHING_HOME'));

    ob_start(array('pakePhingTask', 'colorize'), 2);
    $m = new pakePhing();
    $m->execute($args);
    $m->runBuild();
    ob_end_clean();
  }

  public static function colorize($text)
  {
    return preg_replace(array(
      '#\[(.+?)\]#',
      '#{{PHP Error}}#e',
      '#({{.+?}})#e',
      '#(\+ [^ ]+)#e',
      '#{{(.+?)}}#',
    ), array(
      '{{$1}}',
      'pakeColor::colorize("(PHP Error)", "ERROR")',
      'pakeColor::colorize("$1", "INFO")',
      'pakeColor::colorize("$1", "INFO")',
      '[$1]',
    ), $text);
  }
}

class pakePhing extends Phing
{
  function getPhingVersion()
  {
    return 'pakePhing';
  }
}
