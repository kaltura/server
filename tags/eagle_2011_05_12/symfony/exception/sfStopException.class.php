<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfStopException is thrown when you want to stop action flow.
 *
 * @package    symfony
 * @subpackage exception
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfStopException.class.php 3243 2007-01-12 14:22:50Z fabien $
 */
class sfStopException extends sfException
{
  /**
   * Class constructor.
   *
   * @param string The error message
   * @param int    The error code
   */
  public function __construct($message = null, $code = 0)
  {
    $this->setName('sfStopException');

    // disable xdebug to avoid backtrace in error log
    if (function_exists('xdebug_disable'))
    {
      xdebug_disable();
    }

    parent::__construct($message, $code);
  }

  /**
   * Stops the current action.
   */
  public function printStackTrace($exception = null)
  {
  }
}
