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
 * sfActions executes all the logic for the current request.
 *
 * @package    symfony
 * @subpackage action
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @author     Sean Kerr <skerr@mojavi.org>
 * @version    SVN: $Id: sfActions.class.php 3198 2007-01-08 20:36:20Z fabien $
 */
abstract class sfActions extends sfAction
{
  /**
   * Dispatches to the action defined by the 'action' parameter of the sfRequest object.
   *
   * This method try to execute the executeXXX() method of the current object where XXX is the
   * defined action name.
   *
   * @return string A string containing the view name associated with this action
   *
   * @throws sfInitializationException
   *
   * @see sfAction
   */
  public function execute()
  {
    // dispatch action
    $actionToRun = 'execute'.ucfirst($this->getActionName());
    if (!is_callable(array($this, $actionToRun)))
    {
      // action not found
      $error = 'sfAction initialization failed for module "%s", action "%s". You must create a "%s" method.';
      $error = sprintf($error, $this->getModuleName(), $this->getActionName(), $actionToRun);
      throw new sfInitializationException($error);
    }

    if (sfConfig::get('sf_logging_enabled'))
    {
      $this->getContext()->getLogger()->info('{sfAction} call "'.get_class($this).'->'.$actionToRun.'()'.'"');
    }

    // run action
    $ret = $this->$actionToRun();

    return $ret;
  }
}
