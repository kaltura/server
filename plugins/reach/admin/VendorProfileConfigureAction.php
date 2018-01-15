<?php
/**
 * @package plugins.reach
 * @subpackage Admin
 */
class VendorProfileConfigureAction extends KalturaApplicationPlugin
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
		$this->client = Infra_ClientHelper::getClient();
		$partnerId = $this->_getParam('new_partner_id');
		$vendorProfileId = $this->_getParam('vendor_profile_id');
		$action->view->errMessage = null;
		$action->view->form = '';
		$form = null;

		try
		{
			Infra_ClientHelper::impersonate($partnerId);
			if ($vendorProfileId)
				$form = $this->handleExistingVendorProfile($action, $vendorProfileId, $partnerId);
			else
				$form = $this->handleNewVendorProfile($action, $partnerId);
		} catch (Exception $e)
		{
			KalturaLog::err($e->getMessage() . "\n" . $e->getTraceAsString());
			$action->view->errMessage = $e->getMessage();
			if ($form)
			{
				$formData = $action->getRequest()->getPost();
				$form->populate($formData);
				$vendorProfile = $form->getObject('Kaltura_Client_Reach_Type_VendorProfile', $formData, false, true);
			}
		}

		Infra_ClientHelper::unimpersonate();
		$action->view->form = $form;
		$action->view->vendorProfileId = $vendorProfileId;
	}

	/***
	 * @param Zend_Controller_Action $action
	 * @param $vendorProfileId
	 * @param $partnerId
	 * @throws Zend_Form_Exception
	 */
	protected function handleExistingVendorProfile(Zend_Controller_Action $action, $vendorProfileId, $partnerId)
	{
		$reachPluginClient = Kaltura_Client_Reach_Plugin::get($this->client);
		$vendorProfile = $reachPluginClient->vendorProfile->get($vendorProfileId);
		$form = $this->initForm($action, $partnerId, $vendorProfileId);

		$request = $action->getRequest();
		$formData = $request->getPost();
		if ($request->isPost() && $form->isValid($formData))
			$this->handlePost($action, $form, $formData, $vendorProfileId);
		else
			$form->populateFromObject($vendorProfile, false);
		return $form;
	}

	/**
	 * @param Zend_Controller_Action $action
	 * @param $partnerId
	 * @return mixed
	 */
	protected function handleNewVendorProfile(Zend_Controller_Action $action, $partnerId)
	{
		$form = $this->initForm($action, $partnerId);
		$request = $action->getRequest();
		$formData = $request->getPost();
		$form->populate($formData);
		if ($request->isPost() && $form->isValid($formData))
			$this->handlePost($action, $form, $formData);
		else
			$form->getElement('partnerId')->setValue($partnerId);

		return $form;
	}

	/**
	 * @param Zend_Controller_Action $action
	 * @param $form
	 * @param $formData
	 */
	protected function handlePost(Zend_Controller_Action $action, $form, $formData, $vendorProfileId = null)
	{
		$vendorProfile = $form->getObject('Kaltura_Client_Reach_Type_VendorProfile', $formData, false, true);
		$form->populate($formData);
		$form->resetUnUpdatebleAttributes($vendorProfile);
		$reachPluginClient = Kaltura_Client_Reach_Plugin::get($this->client);
		if ($vendorProfileId)
			$vendorProfile = $reachPluginClient->vendorProfile->update($vendorProfileId, $vendorProfile);
		else
			$vendorProfile = $reachPluginClient->vendorProfile->add($vendorProfile);

		$form->setAttrib('class', 'valid');
		$action->view->formValid = true;
	}

	/**
	 * @param Zend_Controller_Action $action
	 * @param $partnerId
	 * @param $vendorProfileId
	 * @return Form_VendorProfileConfigure
	 */
	protected function initForm(Zend_Controller_Action $action, $partnerId, $vendorProfileId = null)
	{
		$urlParams = array(
			'controller' => 'plugin',
			'action' => 'VendorProfileConfigureAction',
		);

		if ($vendorProfileId)
		{
			$blockFields = true;
			$urlParams['vendor_profile_id'] = $vendorProfileId;
		}
		$form = new Form_VendorProfileConfigure($partnerId, $blockFields);
		$form->setAction($action->view->url($urlParams));
		return $form;
	}
}