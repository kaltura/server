<?php

/*
 * This file is part of the pake package.
 * (c) 2004, 2005 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * pakeException is the base class for all pake related exceptions and
 * provides an additional method for printing up a detailed view of an
 * exception.
 *
 * @package    pake
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: pakeException.class.php 2795 2006-11-23 19:51:21Z fabien $
 */
class pakeException extends Exception
{
  public static function strlen($string)
  {
      return function_exists('mb_strlen') ? mb_strlen($string) : strlen($string);
  }

  function render($e)
  {
    $title = '  ['.get_class($e).']  ';
    $len = self::strlen($title);
    $lines = array();
    foreach (explode("\n", $e->getMessage()) as $line)
    {
      $lines[] = '  '.$line.'  ';
      $len = max(self::strlen($line) + 4, $len);
    }
    $messages = array(
      str_repeat(' ', $len),
      $title.str_repeat(' ', $len - self::strlen($title)),
    );

    foreach ($lines as $line)
    {
      $messages[] = $line.str_repeat(' ', $len - self::strlen($line));
    }

    $messages[] = str_repeat(' ', $len);

    fwrite(STDERR, "\n");
    foreach ($messages as $message)
    {
      fwrite(STDERR, pakeColor::colorize($message, 'ERROR', STDERR)."\n");
    }
    fwrite(STDERR, "\n");

    $pake = pakeApp::get_instance();

    if ($pake->get_trace())
    {
      fwrite(STDERR, "exception trace:\n");

      $trace = $this->trace($e);
      for ($i = 0, $count = count($trace); $i < $count; $i++)
      {
        $class = (isset($trace[$i]['class']) ? $trace[$i]['class'] : '');
        $type = (isset($trace[$i]['type']) ? $trace[$i]['type'] : '');
        $function = $trace[$i]['function'];
        $file = isset($trace[$i]['file']) ? $trace[$i]['file'] : 'n/a';
        $line = isset($trace[$i]['line']) ? $trace[$i]['line'] : 'n/a';

        fwrite(STDERR, sprintf(" %s%s%s at %s:%s\n", $class, $type, $function, pakeColor::colorize($file, 'INFO', STDERR), pakeColor::colorize($line, 'INFO', STDERR)));
      }
    }

    fwrite(STDERR, "\n");
  }

  function trace($exception)
  {
    // exception related properties
    $trace = $exception->getTrace();
    array_unshift($trace, array(
      'function' => '',
      'file'     => ($exception->getFile() != null) ? $exception->getFile() : 'n/a',
      'line'     => ($exception->getLine() != null) ? $exception->getLine() : 'n/a',
      'args'     => array(),
    ));

    return $trace;
  }
}
