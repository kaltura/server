<?php
class EventNotificationTemplateConfigureAction extends KalturaAdminConsolePlugin
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
		$form = null;
		
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
			/* @var $form Form_EventNotificationTemplateConfiguration */
			$templateClass = KalturaPluginManager::getObjectClass('Kaltura_Client_EventNotification_Type_EventNotificationTemplate', $type);
			KalturaLog::debug("template class [$templateClass]");
			
			if(!$form || !($form instanceof Form_EventNotificationTemplateConfiguration))
			{
				$action->view->errMessage = "Template form not found for type [$type]";
				return;
			}
			
			$form->setAction($action->view->url(array('controller' => 'plugin', 'action' => 'EventNotificationTemplateConfigureAction')));
			
			$pager = new Kaltura_Client_Type_FilterPager();
			$pager->pageSize = 100;
			
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
				
					if(!$eventNotificationTemplate->partnerId)
						$eventNotificationTemplate->partnerId = 0;
					Infra_ClientHelper::impersonate($eventNotificationTemplate->partnerId);
					$eventNotificationTemplate->partnerId = null;
					$eventNotificationTemplate = $eventNotificationPlugin->eventNotificationTemplate->add($eventNotificationTemplate);
					Infra_ClientHelper::unimpersonate();
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
		$action->view->form = $form;
	}
}

