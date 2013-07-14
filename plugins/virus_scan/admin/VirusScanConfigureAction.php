<?php
/**
 * @package plugins.virusScan
 * @subpackage Admin
 */
class VirusScanConfigureAction extends KalturaApplicationPlugin
{
	public function __construct()
	{
		$this->action = 'VirusScanConfigureAction';
		$this->label = null;
		$this->rootLabel = null;
	}
	
	/**
	 * @return string - absolute file path of the phtml template
	 */
	public function getTemplatePath()
	{
		return realpath(dirname(__FILE__));
	}
	
	public function getRequiredPermissions()
	{
		return array(Kaltura_Client_Enum_PermissionName::SYSTEM_ADMIN_VIRUS_SCAN);
	}
	
		
	public function doAction(Zend_Controller_Action $action)
	{
		$action->getHelper('layout')->disableLayout();
		$profileId = $this->_getParam('profileId');
		$editMode = false;
		if ($profileId != null){
			$editMode = true;
		}
		$client = Infra_ClientHelper::getClient();
		$virusScanPlugin = Kaltura_Client_VirusScan_Plugin::get($client);	
		
		$form = new Form_Partner_VirusScanConfiguration();
		$action->view->formValid = false;
		$request = $action->getRequest();

		if ($request->isPost())
		{
			$formData = $request->getPost();
			if ($form->isValid($formData))
			{
				$profile = $form->getObject("Kaltura_Client_VirusScan_Type_VirusScanProfile", $request->getPost(), false, true);
				$entryFilter = new Kaltura_Client_Type_BaseEntryFilter();
				$entryTypeArray = $request->getPost('entryTypeToFilter');
				if (is_array($entryTypeArray)) {
					$entryFilter->typeIn = implode(',', $entryTypeArray);
				}
				$profile->entryFilter = $entryFilter;
				unset($profile->entryTypeToFilter);

				//update profile
				try
				{
					if ($editMode)
					{	
						$virusScanPlugin->virusScanProfile->update($profileId, $profile);			
					}
					//add new profile
					else
					{
						Infra_ClientHelper::impersonate($profile->partnerId);
						unset($profile->partnerId);
						$virusScanPlugin->virusScanProfile->add($profile);
						Infra_ClientHelper::unimpersonate();		
					}
					$action->view->formValid = true;
				}
				catch (Exception $ex)
				{
					$action->view->formValid = false;
					$action->view->errMessage = $ex->getMessage();
				}
			}
			else
			{
				$form->populate($formData);
				$form->getElement('partnerId')->setValue($this->_getParam('new_partner_id'));
			}	
		}
		else
		{
			$partnerId = $request->getParam('new_partner_id');
			if ($editMode || $partnerId)
			{
				//disable field if $editMode, so partnerId won't change
				$form->getElement('partnerId')->setAttrib('readonly', true);
				$form->getElement('partnerId')->setAttrib('class', 'readonly');
				
				$form->getElement('partnerId')->setValue($request->getParam('new_partner_id'));
				if ($profileId != null) {
					$profile = $virusScanPlugin->virusScanProfile->get($profileId);	
					$form->populateFromObject($profile, false);
						
					//setting multicheck drop down list values
					$typesArr = array();
					if (!empty($profile->entryFilter->typeEqual)) {
						$typesArr[] = $profile->entryFilter->typeEqual;
					}
					else if (!empty($profile->entryFilter->typeIn)) {
						$typesArr = array_map('trim', explode(',', $profile->entryFilter->typeIn));
					}
					$form->getElement('entryTypeToFilter')->setValue($typesArr);
				}
			}
		}
		$action->view->form = $form;
	}		

}

