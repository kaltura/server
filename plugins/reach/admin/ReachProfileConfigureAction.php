<?php
/**
 * @package plugins.reach
 * @subpackage Admin
 */
class ReachProfileConfigureAction extends KalturaApplicationPlugin
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
		$reachProfileId = $this->_getParam('reach_profile_id');
		$action->view->errMessage = null;
		$action->view->form = '';
		$form = null;

		try
		{
			Infra_ClientHelper::impersonate($partnerId);
			if ($reachProfileId)
				$form = $this->handleExistingReachProfile($action, $reachProfileId, $partnerId);
			else
				$form = $this->handleNewReachProfile($action, $partnerId);
		}
		catch (Exception $e)
		{
			KalturaLog::err($e->getMessage() . "\n" . $e->getTraceAsString());
			$action->view->errMessage = $e->getMessage();
			if ($form)
			{
				$formData = $action->getRequest()->getPost();
				$form->populate($formData);
				$reachProfile = $form->getObject('Kaltura_Client_Reach_Type_ReachProfile', $formData, false, true);
			}
		}

		Infra_ClientHelper::unimpersonate();
		$action->view->form = $form;
		$action->view->reachProfileId = $reachProfileId;
	}

	/***
	 * @param Zend_Controller_Action $action
	 * @param $reachProfileId
	 * @param $partnerId
	 * @throws Zend_Form_Exception
	 */
	protected function handleExistingReachProfile(Zend_Controller_Action $action, $reachProfileId, $partnerId)
	{
		$reachPluginClient = Kaltura_Client_Reach_Plugin::get($this->client);
		$reachProfile = $reachPluginClient->reachProfile->get($reachProfileId);
		$creditHandlerClass = get_class($reachProfile->credit);
		$form = $this->initForm($action, $partnerId, $reachProfileId, $creditHandlerClass);

		$request = $action->getRequest();
		$formData = $request->getPost();
		if ($request->isPost() && $form->isValid($formData))
			$this->handleExistingPost($action, $form, $formData, $reachProfile);
		else
			$form->populateFromObject($reachProfile, false);
		return $form;
	}

	/**
	 * @param Zend_Controller_Action $action
	 * @param $partnerId
	 * @return mixed
	 */
	protected function handleNewReachProfile(Zend_Controller_Action $action, $partnerId)
	{
		$request = $action->getRequest();
		$formData = $request->getPost();
		$defaultCreditObj = null;
		if (isset($formData['reachProfileCredit']))
			$defaultCreditObj = $formData['reachProfileCredit']['objectType'];

		$creditHandlerClass = $this->_getParam('creditHandlerClass') != 'Null' ? $this->_getParam('creditHandlerClass') : $defaultCreditObj;
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
	protected function handlePost(Zend_Controller_Action $action, $form, $formData, $reachProfileId = null)
	{
		$reachProfile = $form->getObject('Kaltura_Client_Reach_Type_ReachProfile', $formData, false, true);
		$form->populate($formData);
		$form->resetUnUpdatebleAttributes($reachProfile);
		$reachPluginClient = Kaltura_Client_Reach_Plugin::get($this->client);
		if ($reachProfileId)
			$reachProfile = $reachPluginClient->reachProfile->update($reachProfileId, $reachProfile);
		else
			$reachProfile = $reachPluginClient->reachProfile->add($reachProfile);

		$form->setAttrib('class', 'valid');
		$action->view->formValid = true;
	}

	/**
	 * @param Zend_Controller_Action $action
	 * @param $form
	 * @param $formData
	 */
	protected function handleExistingPost(Zend_Controller_Action $action, $form, $formData, $originalReachProfile = null)
	{
		$reachProfile = $form->getObject('Kaltura_Client_Reach_Type_ReachProfile', $formData, false, true);
		$form->populate($formData);
		$form->resetUnUpdatebleAttributes($reachProfile);
		$reachPluginClient = Kaltura_Client_Reach_Plugin::get($this->client);
		if ($originalReachProfile)
		{
			$this->filterRules($originalReachProfile, $reachProfile);
			$reachProfile = $reachPluginClient->reachProfile->update($originalReachProfile->id, $reachProfile);
		}
		else
			$reachProfile = $reachPluginClient->reachProfile->add($reachProfile);

		$form->setAttrib('class', 'valid');
		$action->view->formValid = true;
	}

	/***
	 * filters out deleted admin console rules , update existing admin console rules, adds new admin console rules.
	 * @param $originalReachProfile
	 * @param $reachProfile
	 */
	private function filterRules($originalReachProfile, $reachProfile)
	{
		$originalRules = $originalReachProfile->rules;

		$originalDescriptionMap = array();
		$actualDescriptionMap = array();
		$filteredRules = array();

		foreach ($originalRules as $originalRule)
			if (!empty($originalRule->description))
				$originalDescriptionMap[] = $originalRule->description;

		//handle added or updated rules
		foreach ($reachProfile->rules as $rule)
		{
			if (!empty($rule->description))
			{
				$actualDescriptionMap[] = $rule->description;
				// in case of new rule add to end of array otherwise replace the rule
				if (!in_array($rule->description, $originalDescriptionMap))
					$originalRules[] = $rule;
				else
				{
					foreach ($originalRules as &$originalRule)
						if ($rule->description == $originalRule->description)
							$originalRule = $rule;
				}
			}
		}

		//handle deleted rules
		foreach ($originalRules as $ruleToFilter)
		{
			if (!empty($ruleToFilter->description))
				if (!in_array($ruleToFilter->description, $actualDescriptionMap))
					continue;
			$filteredRules[] = $ruleToFilter;
		}

		$reachProfile->rules = $filteredRules;
	}

	/**
	 * @param Zend_Controller_Action $action
	 * @param $partnerId
	 * @param $reachProfileId
	 * @param $creditHandlerClass
	 * @return Form_ReachProfileConfigure
	 */
	protected function initForm(Zend_Controller_Action $action, $partnerId, $reachProfileId = null, $creditHandlerClass = null)
	{
		$urlParams = array(
			'controller' => 'plugin',
			'action' => 'ReachProfileConfigureAction',
		);

		$blockFields = null;
		if ($reachProfileId)
		{
			$blockFields = true;
			$urlParams['reach_profile_id'] = $reachProfileId;
		}
		$form = new Form_ReachProfileConfigure($partnerId, $blockFields);
		$form->setAction($action->view->url($urlParams));

		$creditHandlerForm = $this->getCreditHandlerForm($creditHandlerClass);

		if (is_null($creditHandlerForm))
			throw new Exception("Can't instantiate reach profile credit form of type $creditHandlerClass");
		$creditHandlerForm->updateCreditOptions($this->getReachProfileCreditClasses($action));
		$form->addSubForm($creditHandlerForm, "reachProfileCredit");
		return $form;
	}

	protected function getCreditHandlerForm($type)
	{
		switch ($type)
		{
			case 'Null':
				return new Form_ReachProfileNullCredit();
			case 'Kaltura_Client_Reach_Type_VendorCredit':
				return new Form_ReachProfileCredit();
			case 'Kaltura_Client_Reach_Type_UnlimitedVendorCredit':
				return new Form_ReachProfileUnlimitedCredit();
			case 'Kaltura_Client_Reach_Type_ReoccurringVendorCredit':
				return new Form_ReachProfileRecurringCredit();
			case 'Kaltura_Client_Reach_Type_TimeRangeVendorCredit':
				return new Form_ReachProfileTimeFramedCredit();
			default:
				return new Form_ReachProfileNullCredit();
		}
	}

	public function getReachProfileCreditFormAction(Zend_Controller_Action $action)
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

	protected function getReachProfileCreditClasses($action)
	{
		$credits = array();
		$credits['Null'] = $action->view->translate('Choose Credit Type');
		$credits['Kaltura_Client_Reach_Type_VendorCredit'] = $action->view->translate('Kaltura_Client_Reach_Type_VendorCredit');
		$credits['Kaltura_Client_Reach_Type_UnlimitedVendorCredit'] = $action->view->translate('Kaltura_Client_Reach_Type_UnlimitedVendorCredit');
		$credits['Kaltura_Client_Reach_Type_ReoccurringVendorCredit'] = $action->view->translate('Kaltura_Client_Reach_Type_ReoccurringVendorCredit');
		$credits['Kaltura_Client_Reach_Type_TimeRangeVendorCredit'] = $action->view->translate('Kaltura_Client_Reach_Type_TimeRangeVendorCredit');

		return $credits;
	}
}
