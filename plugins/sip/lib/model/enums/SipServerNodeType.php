<?php
/**
 * @package plugins.sip
 * @subpackage lib.enum
 */
class SipServerNodeType implements IKalturaPluginEnum, serverNodeType
{
	const SIP_SERVER = 'SIP_SERVER';
	
	public static function getAdditionalValues()
	{
		return array(
			'SIP_SERVER' => self::SIP_SERVER,
		);
	}
	
	/**
	 * @return array
	 */
	public static function getAdditionalDescriptions()
	{
		return array();
	}
}