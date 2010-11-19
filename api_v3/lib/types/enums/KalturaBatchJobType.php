<?php
/**
 * @package api
 * @subpackage enum
 */
class KalturaBatchJobType extends KalturaDynamicEnum implements BatchJobType
{
	/**
	 * @return string
	 */
	public static function getEnumClass()
	{
		return 'BatchJobType';
	}
	
	/**
	 * @param string $const
	 * @param string $type
	 * @return int
	 */
	public static function getCoreValue($valueName, $type = __CLASS__)
	{
		return parent::getCoreValue($valueName, $type);
	}
}
