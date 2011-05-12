<?php

/**
 * @package    pake
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @copyright  2004-2005 Fabien Potencier <fabien.potencier@symfony-project.com>
 * @license    see the LICENSE file included in the distribution
 * @version    SVN: $Id: pakeFileTask.class.php 1791 2006-08-23 21:17:06Z fabien $
 */
 
/**
 *
 * .
 *
 * .
 *
 * @package    pake
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @copyright  2004-2005 Fabien Potencier <fabien.potencier@symfony-project.com>
 * @license    see the LICENSE file included in the distribution
 * @version    SVN: $Id: pakeFileTask.class.php 1791 2006-08-23 21:17:06Z fabien $
 */
class pakeFileTask extends pakeTask
{
  public function is_needed()
  {
    if (!file_exists($this->get_name())) return true;
    $latest_prereq = 0;
    foreach ($this->prerequisites as $prerequisite)
    {
      $t = pakeTask::get($prerequisite)->timestamp();
      if ($t > $latest_prereq)
      {
        $latest_prereq = $t;
      }
    }

    if ($latest_prereq == 0)
    {
      return false;
    }

    return ($this->timestamp() < $latest_prereq);
  }

  public function timestamp()
  {
    if (!file_exists($this->get_name()))
    {
      throw new pakeException(sprintf('File "%s" does not exist!', $this->get_name()));
    }

    $stats = stat($this->get_name());

    return $stats['mtime'];
  }

  public static function define_task($name, $deps = null)
  {
     $task = pakeTask::lookup($name, 'pakeFileTask');
     $task->add_comment();
     $task->enhance($deps);
  }
}
