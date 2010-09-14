<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Output escaping object decorator that intercepts all method calls and escapes
 * their return values.
 *
 * @see        sfOutputEscaper
 * @package    symfony
 * @subpackage view
 * @author     Mike Squire <mike@somosis.co.uk>
 * @version    SVN: $Id: sfOutputEscaperObjectDecorator.class.php 3232 2007-01-11 20:51:54Z fabien $
 */
class sfOutputEscaperObjectDecorator extends sfOutputEscaperGetterDecorator
{
  /**
   * Magic PHP method that intercepts method calls, calls them on the objects
   * that is being escaped and escapes the result.
   *
   * The calling of the method is changed slightly to accommodate passing a
   * specific escaping strategy. An additional parameter is appended to the
   * argument list which is the escaping strategy. The decorator will remove
   * and use this parameter as the escaping strategy if it begins with 'esc_'
   * (the prefix all escaping helper functions have).
   *
   * For example if an object, $o, implements methods a() and b($arg):
   *
   *   $o->a()                // Escapes the return value of a()
   *   $o->a(ESC_RAW)         // Uses the escaping method ESC_RAW with a()
   *   $o->b('a')             // Escapes the return value of b('a')
   *   $o->b('a', ESC_RAW);   // Uses the escaping method ESC_RAW with b('a')
   *
   * @param string The method on the object to be called
   * @param array An array of arguments to be passed to the method
   *
   * @return mixed The escaped value returned by the method
   */
  public function __call($method, $args)
  {
    if (count($args) > 0)
    {
      $escapingMethod = $args[count($args) - 1];
      if (is_string($escapingMethod) && substr($escapingMethod, 0, 4) === 'esc_')
      {
        array_pop($args);
      }
      else
      {
        $escapingMethod = $this->escapingMethod;
      }
    }
    else
    {
      $escapingMethod = $this->escapingMethod;
    }

    $value = call_user_func_array(array($this->value, $method), $args);

    return sfOutputEscaper::escape($escapingMethod, $value);
  }

  /**
   * Returns the result of calling the get() method on the object, bypassing
   * any escaping, if that method exists.
   *
   * If there is not a callable get() method this will throw an exception.
   *
   * @param string The parameter to be passed to the get() get method
   *
   * @return mixed The unescaped value returned
   *
   * @throws <b>sfException</b> if the object does not have a callable get() method
   */
  public function getRaw($key)
  {
    if (!is_callable(array($this->value, 'get')))
    {
      throw new sfException('Object does not have a callable get() method.');
    }

    return $this->value->get($key);
  }

  /**
   * Try to call decorated object __toString() method if exists.
   *
   * @return string
   *
   * @throws <b>sfException</b>
   */
  public function __toString()
  {
    if (method_exists($this->value, '__toString'))
    {
      return $this->value->__toString();
    }
    else
    {
      throw new sfException(sprintf('Object of class "%s" cannot be converted to string (Please create a __toString() method)', get_class($this->value)));
    }
  }
}
