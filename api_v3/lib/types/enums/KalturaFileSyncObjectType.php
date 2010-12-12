<?php
class KalturaFileSyncObjectType extends KalturaDynamicEnum implements FileSyncObjectType 
{
	/**
	 * @return string
	 */
	public static function getEnumClass()
	{
		return 'FileSyncObjectType';
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