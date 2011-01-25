<?php
/**
 * @package plugins.contentDistribution
 * @subpackage api.enum
 */
class KalturaDistributionProviderType extends KalturaDynamicEnum implements DistributionProviderType
{
	public static function getEnumClass()
	{
		return 'DistributionProviderType';
	}
}