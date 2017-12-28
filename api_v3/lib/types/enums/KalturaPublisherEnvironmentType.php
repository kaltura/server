<?php
/**
 * @package api
 * @subpackage enum
 */
class KalturaPublisherEnvironmentType extends KalturaDynamicEnum implements PublisherEnvironmentType
{
	/**
	 * @return string
	 */
	public static function getEnumClass()
	{
		return 'PublisherEnvironmentType';
	}
}