<?php
/**
 * @package plugins.sso
 * @subpackage api.enum
 */
class KalturaApplicationType extends KalturaDynamicEnum implements ApplicationType
{
	public static function getEnumClass()
	{
		return 'ApplicationType';
	}
}