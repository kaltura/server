<?php
/**
 * @package api
 * @subpackage enum
 */
class KalturaServerNodeStatus extends KalturaEnum implements ServerNodeStatus
{
	public static function getEnumClass()
	{
		return 'ServerNodeStatus';
	}
}