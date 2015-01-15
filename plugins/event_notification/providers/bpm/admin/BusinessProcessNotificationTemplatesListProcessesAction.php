<?php
/**
 * @package plugins.businessProcessNotification
 * @subpackage admin
 */
class BusinessProcessNotificationTemplatesListProcessesAction extends KalturaApplicationPlugin
{
	public function __construct()
	{
		$this->action = 'listProcessesBusinessProcessNotificationTemplates';
	}
	
	/* (non-PHPdoc)
	 * @see KalturaApplicationPlugin::getTemplatePath()
	 */
	public function getTemplatePath()
	{
		return realpath(dirname(__FILE__));
	}
	
	/* (non-PHPdoc)
	 * @see KalturaApplicationPlugin::getRequiredPermissions()
	 */
	public function getRequiredPermissions()
	{
		return array(Kaltura_Client_Enum_PermissionName::SYSTEM_ADMIN_EVENT_NOTIFICATION_MODIFY);
	}
	
	/* (non-PHPdoc)
	 * @see KalturaApplicationPlugin::doAction()
	 */
	public function doAction(Zend_Controller_Action $action)
	{	
		$action->getHelper('viewRenderer')->setNoRender();
		$serverId = $this->_getParam('server_id');
		
		$client = Infra_ClientHelper::getClient();
		$businessProcessNotificationPlugin = Kaltura_Client_BusinessProcessNotification_Plugin::get($client);
		
		$partnerId = $this->_getParam('partner_id');
		if($partnerId)
			Infra_ClientHelper::impersonate($partnerId);
		
		try{
			$server = $businessProcessNotificationPlugin->businessProcessServer->get($serverId);
			/* @var $server Kaltura_Client_BusinessProcessNotification_Type_BusinessProcessServer */
	
			$businessProcessProvider = kBusinessProcessProvider::get($server);
			$processes = $businessProcessProvider->listBusinessProcesses();
			asort($processes, SORT_STRING | SORT_NATURAL);
		}
		catch(Exception $e)
		{
			KalturaLog::err($e->getMessage() . "\n" . $e->getTraceAsString());
			echo $action->getHelper('json')->sendJson($e->getMessage(), false);
		}
		
		echo $action->getHelper('json')->sendJson($processes, false);
	}
}

