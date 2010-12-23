<?php
class DistributionProfileConfigureAction extends KalturaAdminConsolePlugin
{
	protected $client;
	
	public function __construct()
	{
		$this->action = 'configDistributionProfile';
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
		$this->client = Kaltura_ClientHelper::getClient();
		$request = $action->getRequest();
		
		$profileId = $this->_getParam('profile_id');
		$providerType = null;
		$partnerId = null;
		$distributionProfile = null;
		
		$action->view->errMessage = null;
		$action->view->form = '';
		
		try
		{
			if ($profileId)
			{
				$distributionProfile = $this->client->distributionProfile->get($profileId);
				$providerType = $distributionProfile->providerType;
				$partnerId = $distributionProfile->partnerId;
			}
			else
			{
				$providerType = $this->_getParam('provider_type');
				$partnerId = $this->_getParam('partner_id');
			}
			
			$form = null;
			$profileClass = 'KalturaDistributionProfile';
			
			if($providerType == KalturaDistributionProviderType::GENERIC)
			{
				$form = new Form_GenericProviderProfileConfiguration($partnerId, $providerType);
				$profileClass = 'KalturaGenericDistributionProfile';
			}
			else
			{
				$form = KalturaPluginManager::loadObject('Form_ProviderProfileConfiguration', $providerType, array($partnerId, $providerType));
				$profileClass = KalturaPluginManager::getObjectClass('KalturaDistributionProfile', $providerType);
			}
			
			if(!$form)
			{
				$action->view->errMessage = "Profile form not found for provider [$providerType]";
				return;
			}
			
			$form->setAction($action->view->url(array('controller' => 'plugin', 'action' => 'DistributionProfileConfigureAction')));
			
			$pager = new KalturaFilterPager();
			$pager->pageSize = 100;
			$flavorParamsResponse = $this->client->flavorParams->listAction(null, $pager);
		
			if($profileId)
			{
				if ($request->isPost())
				{
					$form->populate($request->getPost());
					$distributionProfile = $form->getObject($profileClass, $request->getPost());
					$form->resetUnUpdatebleAttributes($distributionProfile);
					$this->client->distributionProfile->update($profileId, $distributionProfile);
				}
				else
				{
					$form->populateFromObject($distributionProfile);
					
					$optionalFlavorParamsIds = array();
					$requiredFlavorParamsIds = array();
					if($distributionProfile->optionalFlavorParamsIds)
						$optionalFlavorParamsIds = explode(',', $distributionProfile->optionalFlavorParamsIds);
					if($distributionProfile->requiredFlavorParamsIds)
						$requiredFlavorParamsIds = explode(',', $distributionProfile->requiredFlavorParamsIds);
						
					$form->addFlavorParamsFields($flavorParamsResponse, $optionalFlavorParamsIds, $requiredFlavorParamsIds);
					
					foreach($distributionProfile->requiredThumbDimensions as $dimensions)
						$form->addThumbDimensions($dimensions, true);
					foreach($distributionProfile->optionalThumbDimensions as $dimensions)
						$form->addThumbDimensions($dimensions, false);
						
					$form->addThumbDimensionsForm();
					$action->view->form = $form;
				}
			}
			else
			{
				if ($request->isPost())
				{
					$form->populate($request->getPost());
					$distributionProfile = $form->getObject($profileClass, $request->getPost());
					
					if(!$distributionProfile->partnerId)
						$distributionProfile->partnerId = 0;
					Kaltura_ClientHelper::impersonate($distributionProfile->partnerId);
					$distributionProfile->partnerId = null;
					$distributionProfile = $this->client->distributionProfile->add($distributionProfile);
					Kaltura_ClientHelper::unimpersonate();
				}
				else 
				{
					$form->addFlavorParamsFields($flavorParamsResponse);
					$form->addThumbDimensionsForm();
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

