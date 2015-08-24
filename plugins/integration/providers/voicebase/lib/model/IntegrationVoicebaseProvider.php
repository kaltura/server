<?php
/**
 * @package plugins.voicebase
 * @subpackage model
 */
class IntegrationVoicebaseProvider implements IIntegrationProvider
{
	/* (non-PHPdoc)
	 * @see IIntegrationProvider::validatePermissions($partnerId)
	 * @return bool
	 */
	public static function validatePermissions($partnerId)
	{
		return PermissionPeer::isAllowedPlugin(VoicebasePlugin::getPluginName(), $partnerId);
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
