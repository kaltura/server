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
 * sfBasicSecurityFilter checks security by calling the getCredential() method
 * of the action. Once the credential has been acquired, sfBasicSecurityFilter
 * verifies the user has the same credential by calling the hasCredential()
 * method of SecurityUser.
 *
 * @package    symfony
 * @subpackage filter
 * @author     Sean Kerr <skerr@mojavi.org>
 * @version    SVN: $Id: sfBasicSecurityFilter.class.php 3244 2007-01-12 14:46:11Z fabien $
 */
class sfBasicSecurityFilter extends sfSecurityFilter
{
  /**
   * Executes this filter.
   *
   * @param sfFilterChain A sfFilterChain instance
   */
  public function execute($filterChain)
  {
    // get the cool stuff
    $context    = $this->getContext();
    $controller = $context->getController();
    $user       = $context->getUser();

    // get the current action instance
    $actionEntry    = $controller->getActionStack()->getLastEntry();
    $actionInstance = $actionEntry->getActionInstance();

    // disable security on [sf_login_module] / [sf_login_action]
    if ((sfConfig::get('sf_login_module') == $context->getModuleName()) && (sfConfig::get('sf_login_action') == $context->getActionName()))
    {
      $filterChain->execute();

      return;
    }

    // get the credential required for this action
    $credential = $actionInstance->getCredential();

    // for this filter, the credentials are a simple privilege array
    // where the first index is the privilege name and the second index
    // is the privilege namespace
    //
    // NOTE: the nice thing about the Action class is that getCredential()
    //       is vague enough to describe any level of security and can be
    //       used to retrieve such data and should never have to be altered
    if ($user->isAuthenticated())
    {
      // the user is authenticated
      if ($credential === null || $user->hasCredential($credential))
      {
        // the user has access, continue
        $filterChain->execute();
      }
      else
      {
        // the user doesn't have access, exit stage left
        $controller->forward(sfConfig::get('sf_secure_module'), sfConfig::get('sf_secure_action'));

        throw new sfStopException();
      }
    }
    else
    {
      // the user is not authenticated
      $controller->forward(sfConfig::get('sf_login_module'), sfConfig::get('sf_login_action'));

      throw new sfStopException();
    }
  }
}
