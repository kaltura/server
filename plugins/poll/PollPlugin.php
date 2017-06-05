<?php
/**
 * @package plugins.poll
 */
class PollPlugin extends KalturaPlugin implements IKalturaServices {

	const PLUGIN_NAME = 'poll';

	/* (non-PHPdoc)
	 * @see IKalturaServices::getPluginName()
	 */
	public static function getPluginName() {
		return self::PLUGIN_NAME;
	}

	/* (non-PHPdoc)
	 * @see IKalturaServices::getServicesMap()
	 */
	public static function getServicesMap()
	{
		$map = array(
			'poll' => 'PollService',
		);
		return $map;
	}

	/* (non-PHPdoc)
	 * @see IKalturaPermissions::isAllowedPartner()
	 */
	public static function isAllowedPartner($partnerId)
	{
		return true;
	}
}
