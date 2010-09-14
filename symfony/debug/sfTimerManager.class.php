<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfTimerManager is a container for sfTimer objects.
 *
 * @package    symfony
 * @subpackage util
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfTimerManager.class.php 3211 2007-01-10 20:51:39Z fabien $
 */
class sfTimerManager
{
  static public $timers = array();

  /**
   * Gets a sfTimer instance.
   *
   * It returns the timer named $name or create a new one if it does not exist.
   *
   * @param string The name of the timer
   *
   * @return sfTimer The timer instance
   */
  public static function getTimer($name)
  {
    if (!isset(self::$timers[$name]))
    {
      self::$timers[$name] = new sfTimer($name);
    }

    self::$timers[$name]->startTimer();

    return self::$timers[$name];
  }

  /**
   * Gets all sfTimer instances stored in sfTimerManager.
   *
   * @return array An array of all sfTimer instances
   */
  public static function getTimers()
  {
    return self::$timers;
  }

  /**
   * Clears all sfTimer instances stored in sfTimerManager.
   */
  public static function clearTimers()
  {
    self::$timers = array();
  }
}
