<?php
class GenericDistributionProviderConfigureAction extends KalturaAdminConsolePlugin
{
	protected $client;
	
	public function __construct()
	{
		$this->action = 'configGenericDistributionProvider';
	}
	
	/**
	 * @return string - absolute file path of the phtml template
	 */
	public function getTemplatePath()
	{
		return realpath(dirname(__FILE__));
	}
	
	public function getRole()
	{
		return Kaltura_AclHelper::ROLE_ADMINISTRATOR;
	}
	
	public function saveProviderActions($providerId, Form_GenericProviderConfiguration $form)
	{
		$this->saveProviderAction($providerId, $form, 'submit', KalturaDistributionAction::SUBMIT);
		$this->saveProviderAction($providerId, $form, 'update', KalturaDistributionAction::UPDATE);
		$this->saveProviderAction($providerId, $form, 'delete', KalturaDistributionAction::DELETE);
		$this->saveProviderAction($providerId, $form, 'fetchReport', KalturaDistributionAction::FETCH_REPORT);
	}
	
	public function saveProviderAction($providerId, Form_GenericProviderConfiguration $form, $action, $actionType)
	{
		$actionObject = null;
		try
		{
			$actionObject = $this->client->genericDistributionProviderAction->getByProviderId($providerId, $actionType);
		}
		catch(Exception $e){}
		
		$isNew = true;
		if($actionObject)
		{
			$isNew = false;
		}
		else
		{
			$actionObject = new KalturaGenericDistributionProviderAction();
			$actionObject->genericDistributionProviderId = $providerId;
			$actionObject->action = $actionType;
		}
			
		$actionObject = $form->getActionObject($actionObject, $action, $actionType);
		
		if(!$actionObject)
		{
			if(!$isNew)
				$this->client->genericDistributionProviderAction->deleteByProviderId($providerId, $actionType);
				
			return;
		}
		
		$genericDistributionProviderAction = null;
		if($isNew)
		{
			$genericDistributionProviderAction = $this->client->genericDistributionProviderAction->add($actionObject);
		}
		else 
		{
			// reset all readonly fields
			$actionObject->id = null;
			$actionObject->createdAt = null;
			$actionObject->updatedAt = null;
			$actionObject->genericDistributionProviderId = null;
			$actionObject->action = null;
			$actionObject->status = null;
			$actionObject->mrssTransformer = null;
			$actionObject->mrssValidator = null;
			$actionObject->resultsTransformer = null;
			
			$genericDistributionProviderAction = $this->client->genericDistributionProviderAction->updateByProviderId($providerId, $actionType, $actionObject);
		}
		
		$genericDistributionProviderActionId = $genericDistributionProviderAction->id;
		KalturaLog::debug("Saved generic distribution provider action [$genericDistributionProviderActionId]");
	
		$upload = new Zend_File_Transfer_Adapter_Http();
		$files = $upload->getFileInfo();
		
		KalturaLog::debug(print_r($files, true));
		if(count($files))
		{
			if(isset($files["mrssTransformer{$action}"]) && $files["mrssTransformer{$action}"]['size'])
			{
				$file = $files["mrssTransformer{$action}"];
				$this->client->genericDistributionProviderAction->addMrssTransformFromFile($genericDistributionProviderActionId, $file['tmp_name']);
			}
		
			if(isset($files["mrssValidator{$action}"]) && $files["mrssValidator{$action}"]['size'])
			{
				$file = $files["mrssValidator{$action}"];
				$this->client->genericDistributionProviderAction->addMrssValidateFromFile($genericDistributionProviderActionId, $file['tmp_name']);
			}
		
			if(isset($files["resultsTransformer{$action}"]) && $files["resultsTransformer{$action}"]['size'])
			{
				$file = $files["resultsTransformer{$action}"];
				$this->client->genericDistributionProviderAction->addResultsTransformFromFile($genericDistributionProviderActionId, $file['tmp_name']);
			}
		}
	}
	
	public function doAction(Zend_Controller_Action $action)
	{
		$action->getHelper('layout')->disableLayout();
		
		$providerId = $this->_getParam('provider_id');
		$this->client = Kaltura_ClientHelper::getClient();
		$form = new Form_GenericProviderConfiguration();
		$form->setAction($action->view->url(array('controller' => 'plugin', 'action' => 'GenericDistributionProviderConfigureAction')));
		
		$request = $action->getRequest();
		
		$pager = new KalturaFilterPager();
		$pager->pageSize = 100;
		$flavorParamsResponse = $this->client->flavorParams->listAction(null, $pager);
			
		$action->view->errMessage = null;
		$action->view->form = '';
		
		try
		{
			if($providerId)
			{
				if ($request->isPost())
				{
					$form->populate($request->getPost());
					$genericDistributionProvider = $form->getObject("KalturaGenericDistributionProvider", $request->getPost());
					$genericDistributionProvider->partnerId = null;
					$this->client->genericDistributionProvider->update($providerId, $genericDistributionProvider);
					$this->saveProviderActions($providerId, $form);
				}
				else
				{
					$genericDistributionProvider = $this->client->genericDistributionProvider->get($providerId);
					$form->populateFromObject($genericDistributionProvider);
					
					$optionalFlavorParamsIds = array();
					$requiredFlavorParamsIds = array();
					if(!is_null($genericDistributionProvider->optionalFlavorParamsIds) && strlen($genericDistributionProvider->optionalFlavorParamsIds))
						$optionalFlavorParamsIds = explode(',', $genericDistributionProvider->optionalFlavorParamsIds);
					if(!is_null($genericDistributionProvider->requiredFlavorParamsIds) && strlen($genericDistributionProvider->requiredFlavorParamsIds))
						$requiredFlavorParamsIds = explode(',', $genericDistributionProvider->requiredFlavorParamsIds);
						
					$form->addFlavorParamsFields($flavorParamsResponse, $optionalFlavorParamsIds, $requiredFlavorParamsIds);
					
					foreach($genericDistributionProvider->requiredThumbDimensions as $dimensions)
						$form->addThumbDimensions($dimensions, true);
					foreach($genericDistributionProvider->optionalThumbDimensions as $dimensions)
						$form->addThumbDimensions($dimensions, false);
						
					$form->addThumbDimensionsForm();
					$form->addProviderActions();
					$form->populateActions($genericDistributionProvider);
					$action->view->form = $form;
				}
			}
			else
			{
				if ($request->isPost())
				{
					$form->populate($request->getPost());
					$genericDistributionProvider = $form->getObject("KalturaGenericDistributionProvider", $request->getPost());
					
					if(!$genericDistributionProvider->partnerId)
						$genericDistributionProvider->partnerId = 0;
					Kaltura_ClientHelper::impersonate($genericDistributionProvider->partnerId);
					$genericDistributionProvider->partnerId = null;
					$genericDistributionProvider = $this->client->genericDistributionProvider->add($genericDistributionProvider);
					$this->saveProviderActions($genericDistributionProvider->id, $form);
					Kaltura_ClientHelper::unimpersonate();
				}
				else 
				{
					$form->addFlavorParamsFields($flavorParamsResponse);
					$form->addThumbDimensionsForm();
					$form->addProviderActions();
					$action->view->form = $form;
				}
			}
		}
		catch(Exception $e)
		{
			KalturaLog::err($e->getMessage() . "\n" . $e->getTraceAsString());
			$action->view->errMessage = $e->getMessage();
		}
	}
}

