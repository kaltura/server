<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Abstract class that provides an interface for escaping of output.
 *
 * @package    symfony
 * @subpackage view
 * @author     Mike Squire <mike@somosis.co.uk>
 * @version    SVN: $Id: sfOutputEscaper.class.php 3232 2007-01-11 20:51:54Z fabien $
 */
abstract class sfOutputEscaper
{
  /**
   * The value that is to be escaped.
   *
   * @var mixed
   */
  protected $value;

  /**
   * The escaping method that is going to be applied to the value and its
   * children. This is actually the name of a PHP function.
   *
   * @var string
   */
  protected $escapingMethod;

  /**
   * Constructor stores the escaping method and value.
   *
   * Since sfOutputEscaper is an abstract class, instances cannot be created
   * directly but the constructor will be inherited by sub-classes.
   *
   * @param string Escaping method
   * @param string Escaping value
   */
  public function __construct($escapingMethod, $value)
  {
    $this->value          = $value;
    $this->escapingMethod = $escapingMethod;
  }

  /**
   * Decorates a PHP variable with something that will escape any data obtained
   * from it.
   *
   * The following cases are dealt with:
   *
   *    - The value is null or false: null or false is returned.
   *    - The value is scalar: the result of applying the escaping method is
   *      returned.
   *    - The value is an array or an object that implements the ArrayAccess
   *      interface: the array is decorated such that accesses to elements yield
   *      an escaped value.
   *    - The value implements the Traversable interface (either an Iterator, an
   *      IteratorAggregate or an internal PHP class that implements
   *      Traversable): decorated much like the array.
   *    - The value is another type of object: decorated such that the result of
   *      method calls is escaped.
   *
   * The escaping method is actually the name of a PHP function. There are a set
   * of standard escaping methods listed in the escaping helper
   * (sfEscapingHelper.php).
   *
   * @param string $escapingMethod the escaping method (a PHP function) to apply to the value
   * @param mixed $value the value to escape
   * @param mixed the escaped value
   *
   * @return mixed Escaping value
   *
   * @throws <b>sfException</b> If the escaping fails
   */
  public static function escape($escapingMethod, $value)
  {
    if (is_null($value) || ($value === false) || ($escapingMethod === 'esc_raw'))
    {
      return $value;
    }

    // Scalars are anything other than arrays, objects and resources.
    if (is_scalar($value))
    {
      return call_user_func($escapingMethod, $value);
    }

    if (is_array($value))
    {
      return new sfOutputEscaperArrayDecorator($escapingMethod, $value);
    }

    if (is_object($value))
    {
      if ($value instanceof sfOutputEscaper)
      {
        // avoid double decoration when passing values from action template to component/partial
        $copy = clone $value;

        $copy->escapingMethod = $escapingMethod;

        return $copy;
      }
      elseif ($value instanceof Traversable)
      {
        return new sfOutputEscaperIteratorDecorator($escapingMethod, $value);
      }
      else
      {
        return new sfOutputEscaperObjectDecorator($escapingMethod, $value);
      }
    }

    // it must be a resource; cannot escape that.
    throw new sfException(sprintf('Unable to escape value "%s"', print_r($value, true)));
  }

  /**
   * Returns the raw value associated with this instance.
   *
   * Concrete instances of sfOutputEscaper classes decorate a value which is
   * stored by the constructor. This returns that original, unescaped, value.
   *
   * @return mixed The original value used to construct the decorator
   */
  public function getRawValue()
  {
    return $this->value;
  }
  
  /**
   * Gets a value from the escaper.
   *
   * @param string Value to get
   *
   * @return mixed Value
   */
  public function __get($var)
  {
    return $this->escape($this->escapingMethod, $this->value->$var);
  }
}
