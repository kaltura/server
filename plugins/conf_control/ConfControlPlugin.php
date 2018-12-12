<?php
/**
 * Sending beacons on various objects
 * @package plugins.confControl
 */
class ConfControlPlugin extends KalturaPlugin implements IKalturaServices, IKalturaPermissions
{

	public static function getPluginName()
	{
		return 'confControl';
	}

	public static function isAllowedPartner($partnerId)
	{
		return ($partnerId == Partner::ADMIN_CONSOLE_PARTNER_ID);
	}

	public static function getServicesMap ()
	{
		$map = array(
			'confControl' => 'ConfControlService',
		);
		return $map;
	}
}