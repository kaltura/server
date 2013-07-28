<?php
/**
 * @package plugins.eventNotification
 * @subpackage admin
 */
class EventNotificationTemplateConfigureAction extends KalturaApplicationPlugin
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
		
		$partnerId = $this->_getParam('partner_id');
		if(!$partnerId)
			$partnerId = 0;
			
		$templateId = $this->_getParam('template_id');
		$cloneTemplateId = $this->_getParam('clone_template_id');
		$type = null;
		$eventNotificationTemplate = null;
		
		$action->view->errMessage = null;
		$action->view->form = '';
		$form = null;
		
		try
		{
			Infra_ClientHelper::impersonate($partnerId);
			
			if($cloneTemplateId)
			{
				$eventNotificationTemplate = $eventNotificationPlugin->eventNotificationTemplate->cloneAction($cloneTemplateId);
				$templateId = $eventNotificationTemplate->id;
				$type = $eventNotificationTemplate->type;
			}
			elseif ($templateId)
			{
				$eventNotificationTemplate = $eventNotificationPlugin->eventNotificationTemplate->get($templateId);
				$type = $eventNotificationTemplate->type;
			}
			else
			{
				$type = $this->_getParam('type');
			}
			
			$form = KalturaPluginManager::loadObject('Form_EventNotificationTemplateConfiguration', $type, array($partnerId, $type));
			/* @var $form Form_EventNotificationTemplateConfiguration */
			$templateClass = KalturaPluginManager::getObjectClass('Kaltura_Client_EventNotification_Type_EventNotificationTemplate', $type);
			KalturaLog::debug("template class [$templateClass]");
			
			if(!$form || !($form instanceof Form_EventNotificationTemplateConfiguration))
			{
				$action->view->errMessage = "Template form not found for type [$type]";
				return;
			}
			
			$urlParams = array(
				'controller' => 'plugin', 
				'action' => 'EventNotificationTemplateConfigureAction',
				'clone_template_id' => null,
			);
			if($templateId)
				$urlParams['template_id'] = $templateId;
				
			$form->setAction($action->view->url($urlParams));
			
			if($templateId) // update or clone
			{
				if ($request->isPost())
				{
					if ($form->isValid($request->getPost()))
					{
						$form->populate($request->getPost());
						$eventNotificationTemplate = $form->getObject($templateClass, $request->getPost());
						$form->resetUnUpdatebleAttributes($eventNotificationTemplate);
						$eventNotificationTemplate = $eventNotificationPlugin->eventNotificationTemplate->update($templateId, $eventNotificationTemplate);
						$form->setAttrib('class', 'valid');
						$action->view->formValid = true;
					}
					else
					{
						$form->populate($request->getPost());
						$eventNotificationTemplate = $form->getObject($templateClass, $request->getPost());
					}
					$form->finit();
				}
				else
				{
					$form->populateFromObject($eventNotificationTemplate);
				}
			}
			else // new
			{
				if ($request->isPost() && $form->isValid($request->getPost()))
				{
					$form->populate($request->getPost());
					$eventNotificationTemplate = $form->getObject($templateClass, $request->getPost());
					$eventNotificationTemplate->partnerId = null;
					$eventNotificationTemplate = $eventNotificationPlugin->eventNotificationTemplate->add($eventNotificationTemplate);
					$form->setAttrib('class', 'valid');
					$action->view->formValid = true;
				}
				else
				{
					$form->populate($request->getPost());
					$eventNotificationTemplate = $form->getObject($templateClass, $request->getPost());
				}
				$form->finit();
			}
		}
		catch(Exception $e)
		{
			KalturaLog::err($e->getMessage() . "\n" . $e->getTraceAsString());
			$action->view->errMessage = $e->getMessage();
			
			if($form)
			{
				$form->populate($request->getPost());
				$eventNotificationTemplate = $form->getObject($templateClass, $request->getPost());
			}
		}
		Infra_ClientHelper::unimpersonate();
		
		$action->view->form = $form;
		$action->view->templateId = $templateId;
		$action->view->plugins = array();
		
		$pluginInstances = KalturaPluginManager::getPluginInstances('IKalturaApplicationPartialView');
		KalturaLog::debug("plugin instances [" . count($pluginInstances) . "]");
		foreach($pluginInstances as $pluginInstance)
		{
			$entryInvestigatePlugins = $pluginInstance->getApplicationPartialViews('plugin', get_class($this));
			if(!$entryInvestigatePlugins)
				continue;
			
			foreach($entryInvestigatePlugins as $plugin)
			{
				/* @var $plugin Kaltura_View_Helper_PartialViewPlugin */
	    		$plugin->plug($action->view);
			}
		}
	}
}

