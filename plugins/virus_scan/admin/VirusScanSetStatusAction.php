<?php
/**
 * @package plugins.virusScan
 * @subpackage Admin
 */
class VirusScanSetStatusAction extends KalturaApplicationPlugin
{
	public function __construct()
	{
		$this->action = 'VirusScanSetStatusAction';
		$this->label = null;
		$this->rootLabel = null;
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
		return array(Kaltura_Client_Enum_PermissionName::SYSTEM_ADMIN_VIRUS_SCAN);
	}
	
		
	public function doAction(Zend_Controller_Action $action)
	{
		$action->getHelper('layout')->disableLayout();
		$profileId = $this->_getParam('profileId');
		$status = $this->_getParam('status');
		$client = Infra_ClientHelper::getClient();
		$virusScanPlugin = Kaltura_Client_VirusScan_Plugin::get($client);		
		$newVirusScanProfile = new Kaltura_Client_VirusScan_Type_VirusScanProfile();
		
		if ($status == 'enable'){
			$newVirusScanProfile->status = Kaltura_Client_VirusScan_Enum_VirusScanProfileStatus::ENABLED;
		}
		elseif ($status == 'disable'){			
			$newVirusScanProfile->status = Kaltura_Client_VirusScan_Enum_VirusScanProfileStatus::DISABLED;
		}
		elseif ($status == 'delete'){
			$newVirusScanProfile->status = Kaltura_Client_VirusScan_Enum_VirusScanProfileStatus::DELETED;
		}
		try
		{
			$virusScanPlugin->virusScanProfile->update($profileId, $newVirusScanProfile);
			echo $action->getHelper('json')->sendJson('ok', false);
		}
		catch(Exception $e)
		{
			KalturaLog::err($e->getMessage() . "\n" . $e->getTraceAsString());
			echo $action->getHelper('json')->sendJson($e->getMessage(), false);
		}
	}
}

