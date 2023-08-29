<?php

/**
 * corp actions.
 *
 * @package    Core
 * @subpackage corp
 * @deprecated
 */
class corpActions extends sfActions
{
  /**
   * Executes index actionw
   *
   */
  public function executeIndex()
  {
	$this->redirect(kConf::get('corp_action_schema') . kConf::get('corp_action_redirect'), 301);
  }

  public function executeError404()
  {
	$this->redirect(kConf::get('corp_action_schema') . kConf::get('corp_action_redirect'), 301);
  }
  
  public function executeTandc()
  {
	$this->redirect(kConf::get('corp_action_schema') . kConf::get('corp_action_redirect')."/tandc", 301);
  }
}

?>
