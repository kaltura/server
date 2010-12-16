<?php
class GenericDistributionProviderConfigureAction extends KalturaAdminConsolePlugin
{
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
	
	public function doAction(Zend_Controller_Action $action)
	{
		$action->getHelper('layout')->disableLayout();
		
		$providerId = $this->_getParam('provider_id');
		$client = Kaltura_ClientHelper::getClient();
		$form = new Form_GenericProviderConfiguration();
		$form->setAction($action->view->url(array('controller' => 'plugin', 'action' => 'GenericDistributionProviderConfigureAction')));
		
		$request = $action->getRequest();
		
		$flavorParamsResponse = $client->flavorParams->listAction();
			
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
					$client->genericDistributionProvider->update($providerId, $genericDistributionProvider);
				}
				else
				{
					$genericDistributionProvider = $client->genericDistributionProvider->get($providerId);
					$form->populateFromObject($genericDistributionProvider);
					
					$optionalFlavorParamsIds = array();
					$requiredFlavorParamsIds = array();
					if($genericDistributionProvider->optionalFlavorParamsIds)
						$optionalFlavorParamsIds = explode(',', $genericDistributionProvider->optionalFlavorParamsIds);
					if($genericDistributionProvider->requiredFlavorParamsIds)
						$requiredFlavorParamsIds = explode(',', $genericDistributionProvider->requiredFlavorParamsIds);
						
					$form->addFlavorParamsFields($flavorParamsResponse, $optionalFlavorParamsIds, $requiredFlavorParamsIds);
					
					foreach($genericDistributionProvider->requiredThumbDimensions as $dimensions)
						$form->addThumbDimensions($dimensions, true);
					foreach($genericDistributionProvider->optionalThumbDimensions as $dimensions)
						$form->addThumbDimensions($dimensions, false);
						
					$form->addThumbDimensionsForm();
					$form->addProviderActions();
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
					$client->genericDistributionProvider->add($genericDistributionProvider);
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
			KalturaLog::err($e->getMessage());
			$action->view->errMessage = $e->getMessage();
		}
	}
}

