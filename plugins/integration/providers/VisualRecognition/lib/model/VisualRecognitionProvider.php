<?php
/**
 * @package plugins.visualRecognition
 * @subpackage model
 */
class VisualRecognitionProvider implements IIntegrationProvider
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
