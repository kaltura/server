<?php
/**
 * @package api
 * @subpackage enum
 */
class KalturaBatchJobType extends KalturaDynamicEnum implements BatchJobType
{
	public static function getEnumClass()
	{
		return 'BatchJobType';
	}
}
