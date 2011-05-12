<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * defaultActions module.
 *
 * @package    symfony
 * @subpackage action
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: actions.class.php 3313 2007-01-20 07:00:37Z fabien $
 */
class defaultActions extends sfActions
{
  /**
   * Congratulations page for creating an application
   *
   */
  public function executeIndex()
  {
  }

  /**
   * Congratulations page for creating a module
   *
   */
  public function executeModule()
  {
  }

  /**
   * Error page for page not found (404) error
   *
   */
  public function executeError404()
  {
  }

  /**
   * Warning page for restricted area - requires login
   *
   */
  public function executeSecure()
  {
  }

  /**
   * Warning page for restricted area - requires credentials
   *
   */
  public function executeLogin()
  {
  }

  /**
   * Website temporarily unavailable
   *
   */
  public function executeUnavailable()
  {
  }

  /**
   * Website disabled by the site administrator (in settings.yml)
   *
   */
  public function executeDisabled()
  {
  }
}
