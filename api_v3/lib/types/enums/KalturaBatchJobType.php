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
}
