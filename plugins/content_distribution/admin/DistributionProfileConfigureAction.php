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
	
	public function getRequiredPermissions()
	{
		return array(Kaltura_Client_Enum_PermissionName::SYSTEM_ADMIN_CONTENT_DISTRIBUTION_MODIFY);
	}
	
	public function doAction(Zend_Controller_Action $action)
	{
		$action->getHelper('layout')->disableLayout();
		$this->client = Infra_ClientHelper::getClient();
		$contentDistributionPlugin = Kaltura_Client_ContentDistribution_Plugin::get($this->client);
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
				$distributionProfile = $contentDistributionPlugin->distributionProfile->get($profileId);
				$providerType = $distributionProfile->providerType;
				$partnerId = $distributionProfile->partnerId;
			}
			else
			{
				$providerType = $this->_getParam('provider_type');
				$partnerId = $this->_getParam('partner_id');
			}
			
			$form = null;
			$profileClass = 'Kaltura_Client_ContentDistribution_Type_DistributionProfile';
			
			if($providerType == Kaltura_Client_ContentDistribution_Enum_DistributionProviderType::GENERIC)
			{
				$form = new Form_GenericProviderProfileConfiguration($partnerId, $providerType);
				$profileClass = 'KalturaGenericDistributionProfile';
			}
			elseif($providerType == Kaltura_Client_ContentDistribution_Enum_DistributionProviderType::SYNDICATION)
			{
				$form = new Form_SyndicationProviderProfileConfiguration($partnerId, $providerType);
				$profileClass = 'KalturaSyndicationDistributionProfile';
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
			
			$pager = new Kaltura_Client_Type_FilterPager();
			$pager->pageSize = 100;
			$flavorParamsResponse = $this->client->flavorParams->listAction(null, $pager);
		
			if($profileId)
			{
				if ($request->isPost())
				{
					$form->populate($request->getPost());
					$distributionProfile = $form->getObject($profileClass, $request->getPost());
					$form->resetUnUpdatebleAttributes($distributionProfile);
					$distributionProfile = $contentDistributionPlugin->distributionProfile->update($profileId, $distributionProfile);
					$form->saveProviderAdditionalObjects($distributionProfile);
					$form->setAttrib('class', 'valid');
				}
				else
				{
					$form->populateFromObject($distributionProfile);
					
					$optionalFlavorParamsIds = array();
					$requiredFlavorParamsIds = array();
					if(!is_null($distributionProfile->optionalFlavorParamsIds) && strlen($distributionProfile->optionalFlavorParamsIds))
						$optionalFlavorParamsIds = explode(',', $distributionProfile->optionalFlavorParamsIds);
					if(!is_null($distributionProfile->requiredFlavorParamsIds) && strlen($distributionProfile->requiredFlavorParamsIds))
						$requiredFlavorParamsIds = explode(',', $distributionProfile->requiredFlavorParamsIds);
						
					$form->addFlavorParamsFields($flavorParamsResponse, $optionalFlavorParamsIds, $requiredFlavorParamsIds);
					
					if(is_array($distributionProfile->requiredThumbDimensions))
						foreach($distributionProfile->requiredThumbDimensions as $dimensions)
							$form->addThumbDimensions($dimensions, true);
							
					if(is_array($distributionProfile->optionalThumbDimensions))
						foreach($distributionProfile->optionalThumbDimensions as $dimensions)
							$form->addThumbDimensions($dimensions, false);
						
					$form->addThumbDimensionsForm();
				}
				$action->view->form = $form;
			}
			else
			{
				if ($request->isPost())
				{
					$form->populate($request->getPost());
					$distributionProfile = $form->getObject($profileClass, $request->getPost());
					
					if(!$distributionProfile->partnerId)
						$distributionProfile->partnerId = 0;
					Infra_ClientHelper::impersonate($distributionProfile->partnerId);
					$distributionProfile->partnerId = null;
					$distributionProfile = $contentDistributionPlugin->distributionProfile->add($distributionProfile);
					Infra_ClientHelper::unimpersonate();
					$form->saveProviderAdditionalObjects($distributionProfile);
					$form->setAttrib('class', 'valid');
				}
				else 
				{
					$form->addFlavorParamsFields($flavorParamsResponse);
					$form->addThumbDimensionsForm();
				}
				$action->view->form = $form;
			}
		}
		catch(Exception $e)
		{
			KalturaLog::err($e->getMessage() . "\n" . $e->getTraceAsString());
			$action->view->errMessage = $e->getMessage();
		}
	}
}

