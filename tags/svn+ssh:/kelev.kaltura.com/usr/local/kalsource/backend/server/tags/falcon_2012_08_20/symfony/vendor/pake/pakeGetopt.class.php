<?php

/**
 * @package    pake
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @copyright  2004-2005 Fabien Potencier <fabien.potencier@symfony-project.com>
 * @license    see the LICENSE file included in the distribution
 * @version    SVN: $Id: pakeGetopt.class.php 1791 2006-08-23 21:17:06Z fabien $
 */

if (class_exists('pakeGetopt'))
{
  return;
}

/**
 *
 * Console options parsing class.
 *
 * @package    pake
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @copyright  2004-2005 Fabien Potencier <fabien.potencier@symfony-project.com>
 * @license    see the LICENSE file included in the distribution
 * @version    SVN: $Id: pakeGetopt.class.php 1791 2006-08-23 21:17:06Z fabien $
 */
class pakeGetopt
{
  const NO_ARGUMENT = 0;
  const REQUIRED_ARGUMENT = 1;
  const OPTIONAL_ARGUMENT = 2;
  private $short_options = array();
  private $long_options = array();
  private $args = '';
  private $options = array();
  private $arguments = array();

  public function __construct($options)
  {
    $this->args = '';
    foreach ($options as $option)
    {
      if (!$option[0])
      {
        throw new pakeException(sprintf("pakeGetopt: You must define a long option name! for option %s (%s).", $option[1], $option[3]));
      }

      $this->add_option($option[0], $option[1], $option[2], $option[3]);
    }
  }

  public function add_option($long_opt, $short_opt, $mode = self::NO_ARGUMENT, $comment = '')
  {
    if ($long_opt{0} == '-' && $long_opt{1} == '-')
    {
      $long_opt = substr($long_opt, 2);
    }

    if ($short_opt)
    {
      if ($short_opt{0} == '-')
      {
        $short_opt = substr($short_opt, 1);
      }
      $this->short_options[$short_opt] = array('mode' => $mode, 'comment' => $comment, 'name' => $long_opt);
    }

    $this->long_options[$long_opt] = array('mode' => $mode, 'comment' => $comment, 'name' => $long_opt);
  }

  public function parse($args = null)
  {
    if (is_string($args))
    {
      // hack to split arguments with spaces : --test="with some spaces"
      $args = preg_replace('/(\'|")(.+?)\\1/e', "str_replace(' ', '=PLACEHOLDER=', '\\2')", $args);
      $args = preg_split('/\s+/', $args);
      $args = str_replace('=PLACEHOLDER=', ' ', $args);
    }
    else if (!$args)
    {
      $args = $this->read_php_argv();

      // we strip command line program
      if (isset($args[0]) && $args[0]{0} != '-')
      {
        array_shift($args);
      }
    }

    $this->args = $args;

    $this->options = array();
    $this->arguments = array();

    while ($arg = array_shift($this->args))
    {
      /* '--' stop options parsing. */
      if ($arg == '--')
      {
        $this->arguments = array_merge($this->arguments, $this->args);
        break;
      }

      if ($arg{0} != '-' || (strlen($arg) > 1 && $arg{1} == '-' && !$this->long_options))
      {
        $this->arguments = array_merge($this->arguments, array($arg), $this->args);
        break;
      }
      elseif (strlen($arg) > 1 && $arg{1} == '-')
      {
        $this->parse_long_option(substr($arg, 2));
      }
      else
      {
        $this->parse_short_option(substr($arg, 1));
      }
    }
  }

  public function has_option($option)
  {
    return (array_key_exists($option, $this->options) ? true : false);
  }

  public function get_option($option)
  {
    // is it a long option?
    if (array_key_exists($option, $this->long_options) && $this->long_options[$option]['mode'] != self::NO_ARGUMENT)
    {
      return (array_key_exists($option, $this->options) ? $this->options[$option] : '');
    }
    else
    {
      throw new pakeException('pakeGetopt: You cannot get a value for a NO_ARGUMENT option.');
    }
  }

  public function get_options()
  {
    return $this->options;
  }

  public function get_arguments()
  {
    return $this->arguments;
  }

  private function parse_short_option($arg)
  {
    for ($i = 0; $i < strlen($arg); $i++)
    {
      $opt = $arg{$i};
      $opt_arg = true;

      /* option exists? */
      if (!array_key_exists($opt, $this->short_options))
      {
        throw new pakeException(sprintf("pakeGetopt: unrecognized option -%s.", $opt));
      }

      /* required or optional argument? */
      if ($this->short_options[$opt]['mode'] == self::REQUIRED_ARGUMENT)
      {
        if ($i + 1 < strlen($arg))
        {
          $this->options[$this->short_options[$opt]['name']] = substr($arg, $i + 1);
          break;
        }
        else
        {
          // take next element as argument (if it doesn't start with a -)
          if (count($this->args) && $this->args[0]{0} != '-')
          {
            $this->options[$this->short_options[$opt]['name']] = array_shift($this->args);
            break;
          }
          else
          {
            throw new pakeException(sprintf("pakeGetopt: option -%s requires an argument", $opt));
          }
        }
      }
      else if ($this->short_options[$opt]['mode'] == self::OPTIONAL_ARGUMENT)
      {
        if (substr($arg, $i + 1) != '')
        {
          $this->options[$this->short_options[$opt]['name']] = substr($arg, $i + 1);
        }
        else
        {
          // take next element as argument (if it doesn't start with a -)
          if (count($this->args) && $this->args[0]{0} != '-')
          {
            $this->options[$this->short_options[$opt]['name']] = array_shift($this->args);
          }
          else
          {
            $this->options[$this->short_options[$opt]['name']] = true;
          }
        }

        break;
      }

      $this->options[$this->short_options[$opt]['name']] = $opt_arg;
    }
  }

  private function parse_long_option($arg)
  {
    @list($opt, $opt_arg) = explode('=', $arg);

    if (!$opt_arg)
    {
      $opt_arg = true;
    }

    /* option exists? */
    if (!array_key_exists($opt, $this->long_options))
    {
      throw new pakeException(sprintf("pakeGetopt: unrecognized option --%s.", $opt));
    }

    /* required or optional argument? */
    if ($this->long_options[$opt]['mode'] == self::REQUIRED_ARGUMENT)
    {
      if ($opt_arg)
      {
        $this->options[$this->long_options[$opt]['name']] = $opt_arg;
        return;
      }
      else
      {
        throw new pakeException(sprintf("pakeGetopt: option --%s requires an argument.", $opt));
      }
    }
    else if ($this->long_options[$opt]['mode'] == self::OPTIONAL_ARGUMENT)
    {
      $this->options[$this->long_options[$opt]['name']] = $opt_arg;
      return;
    }
    else
    {
      $this->options[$this->long_options[$opt]['name']] = true;
    }
  }

  /**
   * Function from PEAR::Console_Getopt.
   * Safely read the $argv PHP array across different PHP configurations.
   * Will take care on register_globals and register_argc_argv ini directives
   *
   * @access public
   * @return mixed the $argv PHP array
   */
  private function read_php_argv()
  {
    global $argv;
    if (!is_array($argv))
    {
      if (!@is_array($_SERVER['argv']))
      {
        if (!@is_array($GLOBALS['HTTP_SERVER_VARS']['argv']))
        {
          throw new pakeException("pakeGetopt: Could not read cmd args (register_argc_argv=Off?).");
        }

        return $GLOBALS['HTTP_SERVER_VARS']['argv'];
      }
      return $_SERVER['argv'];
    }
    return $argv;
  }
}
