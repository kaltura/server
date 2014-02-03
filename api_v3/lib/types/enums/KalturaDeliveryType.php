<?php
/**
 * @package api
 * @subpackage enum
 */
class KalturaDeliveryType extends KalturaDynamicEnum implements DeliveryType
{
	/**
	 * @return string
	 */
	public static function getEnumClass()
	{
		return 'DeliveryType';
	}
}
