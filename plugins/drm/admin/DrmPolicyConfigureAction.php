<?php
/**
 * @package plugins.drm
 * @subpackage Admin
 */
class DrmPolicyConfigureAction extends KalturaApplicationPlugin
{	
	/**
	 * @return string - absolute file path of the phtml template
	 */
	public function getTemplatePath()
	{
		return realpath(dirname(__FILE__));
	}
	
	public function getRequiredPermissions()
	{
		return array(Kaltura_Client_Enum_PermissionName::SYSTEM_ADMIN_DRM_POLICY_MODIFY);
	}
	
	public function doAction(Zend_Controller_Action $action)
	{
		$action->getHelper('layout')->disableLayout();
		$request = $action->getRequest();
		$drmPolicyId = $this->_getParam('drm_policy_id');
		$configureForm = null;
		$action->view->formValid = false;
		
		try
		{
			// Upon Save (after new or update)
			if ($request->isPost())
			{
				$partnerId = $this->_getParam('partnerId');
				$drmProvider = $this->_getParam('provider');

				$configureForm = new Form_DrmPolicyConfigure($partnerId, $drmProvider);
				$action->view->formValid = $this->processForm($configureForm, $request->getPost(), $partnerId, $drmPolicyId);
			}
			else
			{
				// Upon Create new
				if (is_null($drmPolicyId))
				{
					$partnerId = $this->_getParam('new_partner_id');
					$drmProvider = $this->_getParam('new_drm_provider');

					$configureForm = new Form_DrmPolicyConfigure($partnerId, $drmProvider);
					$configureForm->getElement('partnerId')->setValue($partnerId);
				}
				// Upon Configure on a specific policy
				else
				{
					$client = Infra_ClientHelper::getClient();
					$drmPluginClient = Kaltura_Client_Drm_Plugin::get($client);
					$drmPolicy = $drmPluginClient->drmPolicy->get($drmPolicyId);

					$configureForm = new Form_DrmPolicyConfigure($drmPolicy->partnerId, $drmPolicy->provider);
					$configureForm->populateFromObject($drmPolicy, false);
				}
			}
		}
		catch(Exception $e)
		{
		    $action->view->formValid = false;
			KalturaLog::err($e->getMessage() . "\n" . $e->getTraceAsString());
			$action->view->errMessage = $e->getMessage();
		}
		
		$action->view->form = $configureForm;
	}
	
	private function processForm(Form_DrmPolicyConfigure $form, $formData, $partnerId, $drmPolicyId = null)
	{
		if ($form->isValid($formData))
		{
			$client = Infra_ClientHelper::getClient();
			$drmPluginClient = Kaltura_Client_Drm_Plugin::get($client);
			
			$drmPolicy = $form->getObject("Kaltura_Client_Drm_Type_DrmPolicy", $formData, false, true);
			unset($drmPolicy->id);
			
			Infra_ClientHelper::impersonate($partnerId);
			if (is_null($drmPolicyId))
			{
				$drmPolicy->status = Kaltura_Client_Drm_Enum_DrmPolicyStatus::ACTIVE;
				$drmPluginClient->drmPolicy->add($drmPolicy);
			}
			else
			{
				$drmPluginClient->drmPolicy->update($drmPolicyId, $drmPolicy);
			}
			Infra_ClientHelper::unimpersonate();
			
			$form->setAttrib('class', 'valid');
			return true;
		}
		else
		{
			$form->populate($formData);
			return false;
		}
	}
	
}

