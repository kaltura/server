<?php
/**
 * @package plugins.game
 * @subpackage enum
 */
class KalturaGameObjectType extends KalturaDynamicEnum implements gameObjectType
{
	public static function getEnumClass()
	{
		return 'gameObjectType';
	}
}