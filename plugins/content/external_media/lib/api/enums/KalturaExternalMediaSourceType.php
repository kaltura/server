<?php
/**
 * @package plugins.externalMedia
 * @subpackage api.enum
 */
class KalturaExternalMediaSourceType extends KalturaDynamicEnum implements ExternalMediaSourceType
{
	public static function getEnumClass()
	{
		return 'ExternalMediaSourceType';
	}
}
