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
 *
 * @package    symfony
 * @subpackage request
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @author     Sean Kerr <skerr@mojavi.org>
 * @version    SVN: $Id: sfConsoleRequest.class.php 3606 2007-03-13 19:01:45Z fabien $
 */
class sfConsoleRequest extends sfRequest
{
  /**
   * Initializes this sfRequest.
   *
   * @param sfContext A sfContext instance
   * @param array   An associative array of initialization parameters
   * @param array   An associative array of initialization attributes
   *
   * @return boolean true, if initialization completes successfully, otherwise false
   *
   * @throws <b>sfInitializationException</b> If an error occurs while initializing this Request
   */
  public function initialize($context, $parameters = array(), $attributes = array())
  {
    parent::initialize($context, $parameters, $attributes);

    $this->getParameterHolder()->add($_SERVER['argv']);
  }

  /**
   * Executes the shutdown procedure.
   *
   */
  public function shutdown()
  {
  }
}
