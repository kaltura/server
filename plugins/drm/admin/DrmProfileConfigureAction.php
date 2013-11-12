<?php
/**
 * @package plugins.drm
 * @subpackage Admin
 */
class DrmProfileConfigureAction extends KalturaApplicationPlugin
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
		return array(Kaltura_Client_Enum_PermissionName::SYSTEM_ADMIN_DRM_PROFILE_MODIFY);
	}
	
	public function doAction(Zend_Controller_Action $action)
	{
		$action->getHelper('layout')->disableLayout();
		$request = $action->getRequest();
		$drmProfileId = $this->_getParam('drm_profile_id');
		$partnerId = $this->_getParam('new_partner_id');
		$drmProfileProvider = $this->_getParam('new_drm_profile_provider');
		$drmProfileForm = null;
		$action->view->formValid = false;
		
		try
		{
			if ($request->isPost())
			{
				$partnerId = $this->_getParam('partnerId');
				$drmProfileProvider = $this->_getParam('provider');
				$drmProfileForm = new Form_DrmProfileConfigure($partnerId, $drmProfileProvider);
				$action->view->formValid = $this->processForm($drmProfileForm, $request->getPost(), $drmProfileId);
				if(!is_null($drmProfileId))
				{
					$drmProfile = $drmProfileForm->getObject("Kaltura_Client_Drm_Type_DrmProfile", $request->getPost(), false, true);
				}
			}
			else
			{
				if (!is_null($drmProfileId))
				{
					$client = Infra_ClientHelper::getClient();
					$drmPluginClient = Kaltura_Client_Drm_Plugin::get($client);
					$drmProfile = $drmPluginClient->drmProfile->get($drmProfileId);
					$partnerId = $drmProfile->partnerId;
					$drmProfileProvider = $drmProfile->provider;
					$drmProfileForm = new Form_DrmProfileConfigure($partnerId, $drmProfileProvider);
					$drmProfileForm->populateFromObject($drmProfile, false);
				}
				else
				{
					$drmProfileForm = new Form_DrmProfileConfigure($partnerId, $drmProfileProvider);
					$drmProfileForm->getElement('partnerId')->setValue($partnerId);					
				}
			}
		}
		catch(Exception $e)
		{
		    $action->view->formValid = false;
			KalturaLog::err($e->getMessage() . "\n" . $e->getTraceAsString());
			$action->view->errMessage = $e->getMessage();
		}
		
		$action->view->form = $drmProfileForm;
	}
	
	private function processForm(Form_DrmProfileConfigure $form, $formData, $drmProfileId = null)
	{
		if ($form->isValid($formData))
		{
			$client = Infra_ClientHelper::getClient();
			$drmPluginClient = Kaltura_Client_Drm_Plugin::get($client);
			
			$drmProfile = $form->getObject("Kaltura_Client_Drm_Type_DrmProfile", $formData, false, true);
			unset($drmProfile->id);
			
			if (is_null($drmProfileId)) {
				$drmProfile->status = Kaltura_Client_Drm_Enum_DrmProfileStatus::ACTIVE;
				$responseDrmProfile = $drmPluginClient->drmProfile->add($drmProfile);
			}
			else {
				$responseDrmProfile = $drmPluginClient->drmProfile->update($drmProfileId, $drmProfile);
			}
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

