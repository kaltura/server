<?php
/**
 * @package plugins.uplynk
 * @subpackage model.enum
 */
class EdgeCastDeliveryProfileType implements IKalturaPluginEnum, DeliveryProfileType
{
	const EDGE_CAST_HTTP = 'EDGE_CAST_HTTP';
	const EDGE_CAST_RTMP = 'EDGE_CAST_RTMP';
	
	public static function getAdditionalValues()
	{
		return array(
			'EDGE_CAST_HTTP' => self::EDGE_CAST_HTTP,
			'EDGE_CAST_RTMP' => self::EDGE_CAST_RTMP,
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
