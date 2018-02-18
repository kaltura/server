<?php
/**
 * @package plugins.reach
 * @subpackage Admin
 */
class VendorProfileCreditConfigureAction extends VendorProfileConfigureAction
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
		if(is_null($form))
			throw new Exception("Can't instantiate vendor profile credit form of type $form");

		$action->view->form = $form;
		$action->view->form->updateCreditOptions($this->getVendorProfileCreditClasses($action));
		$action->view->form->getElement("objectType")->setValue($type);
	}
}