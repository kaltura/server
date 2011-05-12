<?php
class DistributionProfileUpdateStatusAction extends KalturaAdminConsolePlugin
{
	public function __construct()
	{
		$this->action = 'updateStatusDistributionProfile';
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
		$action->getHelper('viewRenderer')->setNoRender();
		$profileId = $this->_getParam('profile_id');
		$status = $this->_getParam('status');
		$client = Infra_ClientHelper::getClient();
		$contentDistributionPlugin = Kaltura_Client_ContentDistribution_Plugin::get($client);
		
		try
		{
			$contentDistributionPlugin->distributionProfile->updateStatus($profileId, $status);
			echo $action->getHelper('json')->sendJson('ok', false);
		}
		catch(Exception $e)
		{
			KalturaLog::err($e->getMessage() . "\n" . $e->getTraceAsString());
			echo $action->getHelper('json')->sendJson($e->getMessage(), false);
		}
	}
}

