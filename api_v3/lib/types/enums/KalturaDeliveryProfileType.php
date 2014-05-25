<?php
/**
 * @package api
 * @subpackage enum
 */
class KalturaDeliveryProfileType extends KalturaDynamicEnum implements DeliveryProfileType
{
	/**
	 * @return string
	 */
	public static function getEnumClass()
	{
		return 'DeliveryProfileType';
	}
}
