<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * (c) 2004-2006 Sean Kerr.
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfSecurityFilter provides a base class that classifies a filter as one that handles security.
 *
 * @package    symfony
 * @subpackage filter
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @author     Sean Kerr <skerr@mojavi.org>
 * @version    SVN: $Id: sfSecurityFilter.class.php 3244 2007-01-12 14:46:11Z fabien $
 */
abstract class sfSecurityFilter extends sfFilter
{
  /**
   * Returns a new instance of a sfSecurityFilter.
   *
   * @param string The security class name
   *
   * @return sfSecurityFilter A sfSecurityFilter implementation instance
   */
  public static function newInstance($class)
  {
    // the class exists
    $object = new $class();

    if (!($object instanceof sfSecurityFilter))
    {
      // the class name is of the wrong type
      $error = 'Class "%s" is not of the type sfSecurityFilter';
      $error = sprintf($error, $class);

      throw new sfFactoryException($error);
    }

    return $object;
  }
}
