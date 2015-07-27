<?php
/**
 * @package api
 * @subpackage enum
 */
class KalturaRecalculateCacheType extends KalturaDynamicEnum implements RecalculateCacheType
{
	/**
	 * @return string
	 */
	public static function getEnumClass()
	{
		return 'RecalculateCacheType';
	}
}
