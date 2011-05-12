<?php

/**
 * @package    pake
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @copyright  2004-2005 Fabien Potencier <fabien.potencier@symfony-project.com>
 * @license    see the LICENSE file included in the distribution
 * @version    SVN: $Id: pakePearTask.class.php 1791 2006-08-23 21:17:06Z fabien $
 */

class pakePearTask
{
  public static function import_default_tasks()
  {
    pake_desc('create a PEAR package');
    pake_task('pakePearTask::pear');
  }

  public static function run_pear($task, $args)
  {
    $results = pake_sh('pear package');
    if ($task->is_verbose())
    {
      echo $results;
    }
  }
}
