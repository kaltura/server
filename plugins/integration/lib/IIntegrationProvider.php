<?php
/**
 * @package plugins.integration
 * @subpackage lib
 * 
 */
interface IIntegrationProvider
{
	/**
	 * @return bool
	 */
	public static function validatePermissions($partnerId);
	
	/**
	 * @return bool
	 */
	public static function shouldSendCallBack();
}
