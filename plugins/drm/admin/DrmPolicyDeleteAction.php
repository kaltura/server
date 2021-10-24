<?php
/**
 * @package plugins.drm
 * @subpackage Admin
 */
class DrmPolicyDeleteAction extends KalturaApplicationPlugin
{
	
	/**
	 * @return string - absolute file path of the phtml template
	 */
	public function getTemplatePath()
	{
		return realpath(dirname(__FILE__));
	}
	
	public function getRequiredPermissions()
	{
		return array(Kaltura_Client_Enum_PermissionName::SYSTEM_ADMIN_DRM_POLICY_MODIFY);
	}
	
	public function doAction(Zend_Controller_Action $action)
	{
		$action->getHelper('layout')->disableLayout();
		$drmPolicyId = $this->_getParam('drmPolicyId');
		
		$client = Infra_ClientHelper::getClient();
		$drmPluginClient= Kaltura_Client_Drm_Plugin::get($client);
		
		try
		{
			$drmPluginClient->drmPolicy->delete($drmPolicyId);
			echo $action->getHelper('json')->sendJson('ok', false);
		}
		catch(Exception $e)
		{
			KalturaLog::err($e->getMessage() . "\n" . $e->getTraceAsString());
			echo $action->getHelper('json')->sendJson($e->getMessage(), false);
		}
	}
}

