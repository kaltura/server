<?php
/**
 * @package plugins.drm
 * @subpackage Admin
 */
class DrmAdminApiAction extends KalturaApplicationPlugin
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

		$action->getHelper('layout')->setLayout('layout_empty');
		$request = $action->getRequest();

		$partnerId = $this->_getParam('pId');
		$drmType = $this->_getParam('drmType');
		$actionApi = $this->_getParam('apiAction');

		$readOnly = !($actionApi == 'Add');
		$adminApiForm = new Form_AdminApiConfigure($partnerId, $drmType, $actionApi);
		$action->view->formValid = false;


		try
		{
			if ($request->isPost())
			{
				KalturaLog::info("qwer - got post");
			}
			else
			{
				KalturaLog::info("no post");
			}
			// if remove just say done.
				// $action->view->formValid = true;
			// if get populate data
			// if ads -> get and populate
			$adminApiForm->populate(array('keyFrm' => 'ttttt'));
		}
		catch(Exception $e)
		{
		    $action->view->formValid = false;
			KalturaLog::err($e->getMessage() . "\n" . $e->getTraceAsString());
			$action->view->errMessage = $e->getMessage();
		}
		
		$action->view->form = $adminApiForm;
	}
	
	private function processForm(Form_AdminApiConfigure $form, $formData)
	{
		if ($form->isValid($formData))
		{
			$form->setAttrib('class', 'valid');
		}
		else
		{
			$form->populate($formData);
			return false;
		}
	}
	
}

