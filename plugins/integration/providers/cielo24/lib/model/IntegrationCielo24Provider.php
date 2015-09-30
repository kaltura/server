<?php
/**
 * @package plugins.cielo24
 * @subpackage model
 */
class IntegrationCielo24Provider implements IIntegrationProvider
{
	/* (non-PHPdoc)
	 * @see IIntegrationProvider::validatePermissions($partnerId)
	 * @return bool
	 */
	public static function validatePermissions($partnerId)
	{
		return PermissionPeer::isAllowedPlugin(Cielo24Plugin::getPluginName(), $partnerId);
	}
	
	/* (non-PHPdoc)
	 * @see IIntegrationProvider::shouldSendCallBack()
	 * @return bool
	 */
	public static function shouldSendCallBack()
	{
		return true;
	}
}
