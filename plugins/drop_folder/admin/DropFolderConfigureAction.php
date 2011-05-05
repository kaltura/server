<?php
class DropFolderConfigureAction extends KalturaAdminConsolePlugin
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
		$dropFolderForm = new Form_DropFolderConfigure();
		if ($request->isPost())
		{
			$this->processForm($dropFolderForm, $request->getPost());
		}
		$action->view->form = $dropFolderForm;
	}
	
	private function processForm(Form_DropFolderConfigure $form, $formData)
	{
		if ($form->isValid($formData))
		{
			try
			{
				$client = Infra_ClientHelper::getClient();
				$dropFolderPluginClient = Kaltura_Client_DropFolder_Plugin::get($client);
				
				$dropFolder = $form->getObject("Kaltura_Client_DropFolder_Type_DropFolder", $request->getPost(), false, true);
				
				$dropFolderId = $newStatus = $this->_getParam('dropFolderId'); //TODO: implement

				if (is_null($dropFolderId)) {
					$responseDropFolder = $dropFolderPluginClient->dropfolder->add($dropFolder);
				}
				else {
					$responseDropFolder = $dropFolderPluginClient->dropfolder->update($dropFolderId, $dropFolder);
				}

				$this->_helper->redirector('DropFolderList');
			}
			catch(Exception $ex)
			{
				//TODO: implement
				throw $ex;
			}
		}
		else
		{
			$form->populate($formData);
		}
	}
}

