<?php
/**
 * @package api
 * @subpackage enum
 */
class KalturaBatchJobObjectType extends KalturaDynamicEnum implements BatchJobObjectType
{
	/**
	 * @return string
	 */
	public static function getEnumClass()
	{
		return 'BatchJobObjectType';
	}
}
