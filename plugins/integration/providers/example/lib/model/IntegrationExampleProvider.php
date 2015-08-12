<?php
/**
 * @package plugins.example
 * @subpackage model
 */
class IntegrationExampleProvider implements IIntegrationProvider
{
	/* (non-PHPdoc)
	 * @see IIntegrationProvider::validatePermissions($partnerId)
	 * @return bool
	 */
	public static function validatePermissions($partnerId)
	{
		return true;
	}

	/* (non-PHPdoc)
	 * @see IIntegrationProvider::shouldSendCallBack()
	 * @return bool
	 */
	public static function shouldSendCallBack()
	{
		return false;
	}
}
