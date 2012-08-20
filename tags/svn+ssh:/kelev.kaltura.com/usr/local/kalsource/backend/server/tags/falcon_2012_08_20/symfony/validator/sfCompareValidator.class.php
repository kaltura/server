<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfCompareValidator checks the equality of two different request parameters.
 *
 * passwordValidator:
 *   class:            sfCompareValidator
 *   param:
 *     check:          password2
 *     compare_error:  The passwords you entered do not match. Please try again.
 *
 * @package    symfony
 * @subpackage validator
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfCompareValidator.class.php 3233 2007-01-11 21:01:08Z fabien $
 */
class sfCompareValidator extends sfValidator
{
  /**
   * Executes this validator.
   *
   * @param mixed A file or parameter value/array
   * @param error An error message reference
   *
   * @return bool true, if this validator executes successfully, otherwise false
   */
  public function execute(&$value, &$error)
  {
    $check_param = $this->getParameterHolder()->get('check');
    $check_value = $this->getContext()->getRequest()->getParameter($check_param);

    if ($value !== $check_value)
    {
      $error = $this->getParameterHolder()->get('compare_error');
      return false;
    }

    return true;
  }

  public function initialize($context, $parameters = null)
  {
    // initialize parent
    parent::initialize($context);

    // set defaults
    $this->getParameterHolder()->set('compare_error', 'Invalid input');

    $this->getParameterHolder()->add($parameters);

    return true;
  }
}
