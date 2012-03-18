<?php
class EventNotificationTemplateUpdateStatusAction extends KalturaAdminConsolePlugin
{
	public function __construct()
	{
		$this->action = 'updateStatusEventNotificationTemplates';
	}
	
	/* (non-PHPdoc)
	 * @see KalturaAdminConsolePlugin::getTemplatePath()
	 */
	public function getTemplatePath()
	{
		return realpath(dirname(__FILE__));
	}
	
	/* (non-PHPdoc)
	 * @see KalturaAdminConsolePlugin::getRequiredPermissions()
	 */
	public function getRequiredPermissions()
	{
		return array(Kaltura_Client_Enum_PermissionName::SYSTEM_ADMIN_EVENT_NOTIFICATION_MODIFY);
	}
	
	/* (non-PHPdoc)
	 * @see KalturaAdminConsolePlugin::doAction()
	 */
	public function doAction(Zend_Controller_Action $action)
	{
		$action->getHelper('viewRenderer')->setNoRender();
		$templateId = $this->_getParam('template_id');
		$status = $this->_getParam('status');
		$client = Infra_ClientHelper::getClient();
		$eventNotificationPlugin = Kaltura_Client_EventNotification_Plugin::get($client);
		
		try
		{
			$eventNotificationPlugin->eventNotificationTemplate->updateStatus($templateId, $status);
			echo $action->getHelper('json')->sendJson('ok', false);
		}
		catch(Exception $e)
		{
			KalturaLog::err($e->getMessage() . "\n" . $e->getTraceAsString());
			echo $action->getHelper('json')->sendJson($e->getMessage(), false);
		}
	}
}

