<?php
/**
 * @package plugins.sso
 * @subpackage Admin
 */
class SsoProfileConfigureAction extends KalturaApplicationPlugin
{
	private $client;

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
		$ssoProfileId = $this->_getParam('sso_profile_id');
		$action->view->errMessage = null;
		$action->view->form = '';
		$form = null;

		try
		{
			if ($ssoProfileId)
				$form = $this->handleExistingSsoProfile($action, $ssoProfileId, $partnerId);
			else
				$form = $this->handleNewSsoProfile($action, $partnerId);
		}
		catch (Exception $e)
		{
			KalturaLog::err($e->getMessage() . "\n" . $e->getTraceAsString());
			$action->view->errMessage = $e->getMessage();
			if ($form)
			{
				$formData = $action->getRequest()->getPost();
				$form->populate($formData);
			}
		}
		$action->view->form = $form;
		$action->view->ssoProfileId = $ssoProfileId;
	}

	/***
	 * @param Zend_Controller_Action $action
	 * @param $ssoProfileId
	 * @param $partnerId
	 * @throws Zend_Form_Exception
	 */
	protected function handleExistingSsoProfile(Zend_Controller_Action $action, $ssoProfileId, $partnerId)
	{
		$ssoPluginClient = Kaltura_Client_Sso_Plugin::get($this->client);
		$request = $action->getRequest();
		$formData = $request->getPost();
		
		$ssoProfile = $ssoPluginClient->sso->get($ssoProfileId);
		
		$form = $this->initForm($action, $partnerId, $ssoProfileId);

		if ($request->isPost() && $form->isValid($formData))
			$this->handleExistingPost($action, $form, $formData, $ssoProfile);
		else
			$form->populateFromObject($ssoProfile, false);
		return $form;
	}

	/**
	 * @param Zend_Controller_Action $action
	 * @param $partnerId
	 * @return mixed
	 */
	protected function handleNewSsoProfile(Zend_Controller_Action $action, $partnerId)
	{
		$request = $action->getRequest();
		$formData = $request->getPost();
		$form = $this->initForm($action, $partnerId);
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
	 * @param $ssoProfileId
	 */
	protected function handlePost(Zend_Controller_Action $action, $form, $formData, $ssoProfileId = null)
	{
		$ssoProfile = $form->getObject('Kaltura_Client_Sso_Type_Sso', $formData, false, true);
		$form->populate($formData);
		$ssoPluginClient = Kaltura_Client_Sso_Plugin::get($this->client);
		if ($ssoProfileId)
			$ssoPluginClient->sso->update($ssoProfileId, $ssoProfile);
		else
			$ssoPluginClient->sso->add($ssoProfile);

		$form->setAttrib('class', 'valid');
		$action->view->formValid = true;
	}

	/**
	 * @param Zend_Controller_Action $action
	 * @param $form
	 * @param $formData
	 * @param $originalssoProfile
	 */
	protected function handleExistingPost(Zend_Controller_Action $action, $form, $formData, $originalssoProfile = null)
	{
		$ssoProfile = $form->getObject('Kaltura_Client_Sso_Type_Sso', $formData, false, true);
		$form->populate($formData);
		$ssoPluginClient = Kaltura_Client_Sso_Plugin::get($this->client);
		if ($originalssoProfile )
		{
			$ssoPluginClient->sso->update($originalssoProfile ->id, $ssoProfile);
		}
		else
		{
			$ssoPluginClient->sso->add($ssoProfile);
		}

		$form->setAttrib('class', 'valid');
		$action->view->formValid = true;
	}

	/**
	 * @param Zend_Controller_Action $action
	 * @param $partnerId
	 * @param $ssoProfileId
	 * @return Form_SsoProfileConfigure
	 */
	protected function initForm(Zend_Controller_Action $action, $partnerId, $ssoProfileId = null)
	{
		$urlParams = array(
			'controller' => 'plugin',
			'action' => 'SsoProfileConfigureAction',
		);

		$blockFields = null;
		if ($ssoProfileId)
		{
			$blockFields = true;
			$urlParams['sso_profile_id'] = $ssoProfileId;
		}
		$form = new Form_SsoProfileConfigure($partnerId, $blockFields);
		$form->setAction($action->view->url($urlParams));

		return $form;
	}
}