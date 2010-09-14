<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Log manager
 *
 * @package    symfony
 * @subpackage log
 * @author     Joe Simms
 * @version    SVN: $Id: sfLogManager.class.php 3329 2007-01-23 08:29:34Z fabien $
 **/
class sfLogManager
{
  /** the default period to rotate logs in days */
  const DEF_PERIOD    = 7;

  /** the default number of log historys to store, one history is created for every period */
  const DEF_HISTORY   = 10;

  /**
   * Rotates log file.
   *
   * @param string Application name
   * @param string Enviroment name
   * @param string Period 
   * @param string History
   * @param boolean Override
   *
   * @author Joe Simms
   **/
  public static function rotate($app, $env, $period = null, $history = null, $override = false)
  {
    $logfile = $app.'_'.$env;
    $logdir = sfConfig::get('sf_log_dir');

    // set history and period values if not passed to default values
    $period = isset($period) ? $period : self::DEF_PERIOD;
    $history = isset($history) ? $history : self::DEF_HISTORY;

    // get todays date
    $today = date('Ymd');

    // check history folder exists
    if (!is_dir($logdir.'/history'))
    {
      mkdir($logdir.'/history', 0777);
    }

    // determine date of last rotation
    $logs = sfFinder::type('file')->ignore_version_control()->maxdepth(1)->name($logfile.'_*.log')->in($logdir.'/history/');
    $recentlog = is_array($logs) ? array_pop($logs) : null;

    if ($recentlog)
    {
      // calculate date to rotate logs on
      $last_rotated_on = filemtime($recentlog);
      $rotate_on = date('Ymd', strtotime('+ '.$period.' days', $last_rotated_on));
    }
    else
    {
      // no rotation has occured yet
      $rotate_on = null;
    }

    $src_log = $logdir.'/'.$logfile.'.log';
    $dest_log = $logdir.'/history/'.$logfile.'_'.$today.'.log';

    // if rotate log on date doesn't exist, or that date is today, then rotate the log
    if (!$rotate_on || ($rotate_on == $today) || $override)
    {
      // create a lock file
      $lock_name = $app.'_'.$env.'.lck';
      touch(sfConfig::get('sf_root_dir').'/'.$lock_name);

      // if log file exists rotate it
      if (file_exists($src_log))
      {
        // check if the log file has already been rotated today
        if (file_exists($dest_log))
        {
          // append log to existing rotated log
          $handle = fopen($dest_log, 'a');
          $append = file_get_contents($src_log);
          fwrite($handle, $append);
        }
        else
        {
          // copy log
          copy($src_log, $dest_log);
        }

        // remove the log file
        unlink($src_log);

        // get all log history files for this application and environment
        $new_logs = sfFinder::type('file')->ignore_version_control()->maxdepth(1)->name($logfile.'_*.log')->in($logdir.'/history/');

        // if the number of logs in history exceeds history then remove the oldest log
        if (count($new_logs) > $history)
        {
          unlink($new_logs[0]);
        }
      }
    }
  }
}
