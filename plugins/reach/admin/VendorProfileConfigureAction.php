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
		$creditHandlerClass = get_class($vendorProfile->credit);
		$form = $this->initForm($action, $partnerId, $vendorProfileId, $creditHandlerClass);

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
		$request = $action->getRequest();
		$formData = $request->getPost();
		$creditHandlerClass = $this->_getParam('creditHandlerClass') != 'Null' ? $this->_getParam('creditHandlerClass') : $formData['vendorProfileCredit']['objectType'];
		$form = $this->initForm($action, $partnerId, null, $creditHandlerClass);
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
	 * @param $creditHandlerClass
	 * @return Form_VendorProfileConfigure
	 */
	protected function initForm(Zend_Controller_Action $action, $partnerId, $vendorProfileId = null, $creditHandlerClass = null)
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

		$creditHandlerForm = $this->getCreditHandlerForm($creditHandlerClass);

		if(is_null($creditHandlerForm))
			throw new Exception("Can't instantiate vendor profile credit form of type $creditHandlerClass");
		$creditHandlerForm->updateCreditOptions($this->getVendorProfileCreditClasses($action));
		$form->addSubForm($creditHandlerForm, "vendorProfileCredit");
		return $form;
	}

	protected function getCreditHandlerForm($type) {
		switch($type) {
			case 'Null':
				return new Form_VendorProfileNullCredit();
			case 'Kaltura_Client_Reach_Type_VendorCredit':
				return new Form_VendorProfileCredit();
			case 'Kaltura_Client_Reach_Type_UnlimitedVendorCredit':
				return new Form_VendorProfileUnlimitedCredit();
			case 'Kaltura_Client_Reach_Type_ReoccurringVendorCredit':
				return new Form_VendorProfileRecurringCredit();
			case 'Kaltura_Client_Reach_Type_TimeRangeVendorCredit':
				return new Form_VendorProfileTimeFramedCredit();
			default:
				return new Form_VendorProfileNullCredit();
		}
	}

	public function getVendorProfileCreditFormAction(Zend_Controller_Action $action)
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

	protected function getVendorProfileCreditClasses($action) {
		$credits = array();
		$credits['Null'] = $action->view->translate('Choose Credit Type');
		$credits['Kaltura_Client_Reach_Type_VendorCredit'] = $action->view->translate('Kaltura_Client_Reach_Type_VendorCredit');
		$credits['Kaltura_Client_Reach_Type_UnlimitedVendorCredit'] = $action->view->translate('Kaltura_Client_Reach_Type_UnlimitedVendorCredit');
		$credits['Kaltura_Client_Reach_Type_ReoccurringVendorCredit'] = $action->view->translate('Kaltura_Client_Reach_Type_ReoccurringVendorCredit');
		$credits['Kaltura_Client_Reach_Type_TimeRangeVendorCredit'] = $action->view->translate('Kaltura_Client_Reach_Type_TimeRangeVendorCredit');

		return $credits;
	}
}