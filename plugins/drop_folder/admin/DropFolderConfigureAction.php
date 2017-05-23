<?php
/**
 * @package plugins.dropFolder
 * @subpackage Admin
 */
class DropFolderConfigureAction extends KalturaApplicationPlugin
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
		return array(Kaltura_Client_Enum_PermissionName::SYSTEM_ADMIN_DROP_FOLDER_MODIFY);
	}
	
	public function doAction(Zend_Controller_Action $action)
	{
		$action->getHelper('layout')->disableLayout();
		$request = $action->getRequest();
		$dropFolderId = $this->_getParam('drop_folder_id');
		$partnerId = $this->_getParam('new_partner_id');
		$dropFolderType = $this->_getParam('new_drop_folder_type');
		$dropFolderForm = null;
		$action->view->formValid = false;
		
		try
		{
			if ($request->isPost())
			{
				$partnerId = $this->_getParam('partnerId');
				$dropFolderType = $this->_getParam('type');
				$dropFolderForm = new Form_DropFolderConfigure($partnerId, $dropFolderType);
				$action->view->formValid = $this->processForm($dropFolderForm, $request->getPost(), $partnerId, $dropFolderId);
				if(!is_null($dropFolderId))
				{
					$dropFolder = $dropFolderForm->getObject("Kaltura_Client_DropFolder_Type_DropFolder", $request->getPost(), false, true);
					$this->disableFileHandlerType($dropFolderForm, $dropFolder->fileHandlerType);									
				}
			}
			else
			{
				if (!is_null($dropFolderId))
				{
					$client = Infra_ClientHelper::getClient();
					$dropFolderPluginClient = Kaltura_Client_DropFolder_Plugin::get($client);
					$dropFolder = $dropFolderPluginClient->dropFolder->get($dropFolderId);
					$partnerId = $dropFolder->partnerId;
					$dropFolderType = $dropFolder->type;
					$dropFolderForm = new Form_DropFolderConfigure($partnerId, $dropFolderType);
					$dropFolderForm->populateFromObject($dropFolder, false);
					$this->disableFileHandlerType($dropFolderForm, $dropFolder->fileHandlerType);
				}
				else
				{
					$dropFolderForm = new Form_DropFolderConfigure($partnerId, $dropFolderType);
					$dropFolderForm->getElement('partnerId')->setValue($partnerId);
					
					$settings = Zend_Registry::get('config')->dropFolder;
					if($dropFolderType ===Kaltura_Client_DropFolder_Enum_DropFolderType::LOCAL)
					{
						$dropFolderForm->getElement('fileSizeCheckInterval')->setValue($settings->fileSizeCheckIntervalLocal);
					}
					else 
					{
						$dropFolderForm->getElement('fileSizeCheckInterval')->setValue($settings->fileSizeCheckIntervalRemote);
					}
					
				}
			}
		}
		catch(Exception $e)
		{
		    $action->view->formValid = false;
			KalturaLog::err($e->getMessage() . "\n" . $e->getTraceAsString());
			$action->view->errMessage = $e->getMessage();
		}
		
		$action->view->form = $dropFolderForm;
	}
	
	private function processForm(Form_DropFolderConfigure $form, $formData, $partnerId, $dropFolderId = null)
	{
		if ($form->isValid($formData))
		{
			$client = Infra_ClientHelper::getClient();
			$dropFolderPluginClient = Kaltura_Client_DropFolder_Plugin::get($client);
			
			$dropFolder = $form->getObject("Kaltura_Client_DropFolder_Type_DropFolder", $formData, false, true);
			$this->validateConversionProfileId($dropFolder->conversionProfileId, $partnerId);
			
			unset($dropFolder->id);
			
			if($dropFolder->fileHandlerType === Kaltura_Client_DropFolder_Enum_DropFolderFileHandlerType::CONTENT)
				$dropFolder->fileNamePatterns = '*';
			if (is_null($dropFolderId)) {
				$dropFolder->status = Kaltura_Client_DropFolder_Enum_DropFolderStatus::ENABLED;
				$responseDropFolder = $dropFolderPluginClient->dropFolder->add($dropFolder);
			}
			else {
				$responseDropFolder = $dropFolderPluginClient->dropFolder->update($dropFolderId, $dropFolder);
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
	
	private function disableFileHandlerType(Form_DropFolderConfigure $dropFolderForm, $fileHandlerTypeValue)
	{
		$fileHandlerType = $dropFolderForm->getElement('fileHandlerType');
		$fileHandlerType->setAttrib('style', 'display:none');
		$fileHandlerType->setLabel('');
		$fileHandlerTypeForView = $dropFolderForm->getElement('fileHandlerTypeForView');
		$fileHandlerTypeForView->setLabel('Ingestion Workflow:');
		$fileHandlerTypeForView->setAttrib('style', 'display:inline');
		$fileHandlerTypeForView->setValue($fileHandlerTypeValue);
	}
	
	protected function validateConversionProfileId ($conversionProfileId, $partnerId)
	{
		$client = Infra_ClientHelper::getClient();
		
		$filter = new Kaltura_Client_Type_ConversionProfileFilter();
		$filter->idEqual = $conversionProfileId;
		Infra_ClientHelper::impersonate($partnerId);
		$conversionProfiles = $client->conversionProfile->listAction($filter);
		Infra_ClientHelper::unimpersonate();
		
		if (!$conversionProfiles->totalCount)
		{
			throw new Exception("Conversion profile ID [$conversionProfileId] not found.");
		}
	}
}

