<?php
class EventNotificationTemplatesConfigureAction extends KalturaAdminConsolePlugin
{
	protected $client;
	
	public function __construct()
	{
		$this->action = 'configEventNotificationTemplate';
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
		$eventNotificationPlugin = Kaltura_Client_EventNotification_Plugin::get($this->client);
		$request = $action->getRequest();
		
		$templateId = $this->_getParam('template_id');
		$type = null;
		$partnerId = null;
		$eventNotificationTemplate = null;
		
		$action->view->errMessage = null;
		$action->view->form = '';
		
		try
		{
			if ($templateId)
			{
				$eventNotificationTemplate = $eventNotificationPlugin->eventNotificationTemplate->get($templateId);
				$type = $eventNotificationTemplate->type;
				$partnerId = $eventNotificationTemplate->partnerId;
			}
			else
			{
				$type = $this->_getParam('type');
				$partnerId = $this->_getParam('partner_id');
			}
			
			$form = KalturaPluginManager::loadObject('Form_EventNotificationTemplateConfiguration', $type, array($partnerId, $type));
			$templateClass = KalturaPluginManager::getObjectClass($templateClass, $type);
			KalturaLog::debug("template class [$templateClass]");
			
			if(!$form)
			{
				$action->view->errMessage = "Template form not found for type [$type]";
				return;
			}
			
			$form->setAction($action->view->url(array('controller' => 'plugin', 'action' => 'EventNotificationTemplateConfigureAction')));
			
			$pager = new Kaltura_Client_Type_FilterPager();
			$pager->pageSize = 100;
			
			Infra_ClientHelper::impersonate($partnerId);
			$flavorParamsResponse = $this->client->flavorParams->listAction(null, $pager);
			Infra_ClientHelper::unimpersonate();
			
			if($templateId) // update
			{
				if ($request->isPost())
				{
					if ($form->isValid($request->getPost()))
					{
						$form->populate($request->getPost());
						$eventNotificationTemplate = $form->getObject($templateClass, $request->getPost());
						$form->resetUnUpdatebleAttributes($eventNotificationTemplate);
						$eventNotificationTemplate = $eventNotificationPlugin->eventNotificationTemplate->update($templateId, $eventNotificationTemplate);
						$form->saveProviderAdditionalObjects($eventNotificationTemplate);
						$form->setAttrib('class', 'valid');
						$action->view->formValid = true;
					}
					else
					{
						$form->populate($request->getPost());
						$eventNotificationTemplate = $form->getObject($templateClass, $request->getPost());
						$this->populateForm($form, $eventNotificationTemplate, $flavorParamsResponse);
					}
				}
				else
				{
					$form->populateFromObject($eventNotificationTemplate);
					$this->populateForm($form, $eventNotificationTemplate, $flavorParamsResponse);
				}
			}
			else // new
			{
				if ($request->isPost() && $form->isValid($request->getPost()))
				{
					$form->populate($request->getPost());
					$eventNotificationTemplate = $form->getObject($templateClass, $request->getPost());
				
					if(!$eventNotificationTemplate->partnerId)
						$eventNotificationTemplate->partnerId = 0;
					Infra_ClientHelper::impersonate($eventNotificationTemplate->partnerId);
					$eventNotificationTemplate->partnerId = null;
					$eventNotificationTemplate = $eventNotificationPlugin->eventNotificationTemplate->add($eventNotificationTemplate);
					Infra_ClientHelper::unimpersonate();
					$form->saveProviderAdditionalObjects($eventNotificationTemplate);
					$form->setAttrib('class', 'valid');
					$action->view->formValid = true;
				}
				else
				{
					$form->populate($request->getPost());
					$eventNotificationTemplate = $form->getObject($templateClass, $request->getPost());
					$this->populateForm($form, $eventNotificationTemplate, $flavorParamsResponse);
				}
			}
		}
		catch(Exception $e)
		{
			KalturaLog::err($e->getMessage() . "\n" . $e->getTraceAsString());
			$action->view->errMessage = $e->getMessage();
			
			$form->populate($request->getPost());
			if (isset($flavorParamsResponse)) 
			{
				$eventNotificationTemplate = $form->getObject($templateClass, $request->getPost());
				$this->populateForm($form, $eventNotificationTemplate, $flavorParamsResponse);
			}
		}
		$action->view->form = $form;
	}
	
	protected function populateForm(Form_DistributionConfiguration $form, Kaltura_Client_EventNotification_Type_EventNotificationTemplate $eventNotificationTemplate, Kaltura_Client_Type_FlavorParamsListResponse $flavorParamsResponse)
	{
		$optionalFlavorParamsIds = array();
		$requiredFlavorParamsIds = array();
		if(!is_null($eventNotificationTemplate->optionalFlavorParamsIds) && strlen($eventNotificationTemplate->optionalFlavorParamsIds))
			$optionalFlavorParamsIds = explode(',', $eventNotificationTemplate->optionalFlavorParamsIds);
		if(!is_null($eventNotificationTemplate->requiredFlavorParamsIds) && strlen($eventNotificationTemplate->requiredFlavorParamsIds))
			$requiredFlavorParamsIds = explode(',', $eventNotificationTemplate->requiredFlavorParamsIds);
			
		$form->addFlavorParamsFields($flavorParamsResponse, $optionalFlavorParamsIds, $requiredFlavorParamsIds);
		
		if(is_array($eventNotificationTemplate->requiredThumbDimensions))
			foreach($eventNotificationTemplate->requiredThumbDimensions as $dimensions)
				$form->addThumbDimensions($dimensions, true);
				
		if(is_array($eventNotificationTemplate->optionalThumbDimensions))
			foreach($eventNotificationTemplate->optionalThumbDimensions as $dimensions)
				$form->addThumbDimensions($dimensions, false);
				
		$form->addThumbDimensionsForm();
	}
}

