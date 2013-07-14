<?php
/**
 * @package api
 * @subpackage enum
 */
class KalturaModerationObjectType extends KalturaDynamicEnum implements moderationObjectType
{
	public static function getEnumClass()
	{
		return 'moderationObjectType';
	}
}