<?php
/**
 * @package plugins.reach
 * @subpackage Admin
 */
class ReachProfileCreditConfigureAction extends ReachProfileConfigureAction
{
	/**
	 * @return string - absolute file path of the phtml template
	 */
	public function getTemplatePath()
	{
		return realpath(dirname(__FILE__));
	}

	public function doAction(Zend_Controller_Action $action)
	{
		$action->getHelper('layout')->disableLayout();
		$type = $action->getRequest()->getParam('creditHandlerClass');
		$form = $this->getCreditHandlerForm($type);
		if (is_null($form))
			throw new Exception("Can't instantiate reach profile credit form of type $type");

		$action->view->form = $form;
		$action->view->form->updateCreditOptions($this->getReachProfileCreditClasses($action));
		$action->view->form->getElement("objectType")->setValue($type);
	}
}