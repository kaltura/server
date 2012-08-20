<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 *
 * @package    symfony
 * @subpackage log
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfWebDebugLogger.class.php 3329 2007-01-23 08:29:34Z fabien $
 */
class sfWebDebugLogger
{
  protected
    $webDebug = null;

  /**
   * Initializes the web debug logger.
   *
   * @param array Logger options
   */
  public function initialize($options = array())
  {
    if (!sfConfig::get('sf_web_debug'))
    {
      return;
    }

    $this->webDebug = sfWebDebug::getInstance();
  }

  /**
   * Logs a message.
   *
   * @param string Message
   * @param string Message priority
   * @param string Message priority name
   */
  public function log($message, $priority, $priorityName)
  {
    if (!sfConfig::get('sf_web_debug'))
    {
      return;
    }

    // if we have xdebug, add some stack information
    $debug_stack = array();
    if (function_exists('xdebug_get_function_stack'))
    {
      foreach (xdebug_get_function_stack() as $i => $stack)
      {
        if (
          (isset($stack['function']) && !in_array($stack['function'], array('emerg', 'alert', 'crit', 'err', 'warning', 'notice', 'info', 'debug', 'log')))
          || !isset($stack['function'])
        )
        {
          $tmp = '';
          if (isset($stack['function']))
          {
            $tmp .= 'in "'.$stack['function'].'" ';
          }
          $tmp .= 'from "'.$stack['file'].'" line '.$stack['line'];
          $debug_stack[] = $tmp;
        }
      }
    }

    // get log type in {}
    $type = 'sfOther';
    if (preg_match('/^\s*{([^}]+)}\s*(.+?)$/', $message, $matches))
    {
      $type    = $matches[1];
      $message = $matches[2];
    }

    // build the object containing the complete log information.
    $logEntry = array(
      'priority'       => $priority,
      'priorityString' => $priorityName,
      'time'           => time(),
      'message'        => $message,
      'type'           => $type,
      'debugStack'     => $debug_stack,
    );

    // send the log object.
    $this->webDebug->log($logEntry);
  }
}
