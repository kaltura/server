<?php
/**
 * @package plugins.huluDistribution
 * @subpackage lib
 */
class HuluDistributionProviderType implements IKalturaPluginEnum, DistributionProviderType
{
	const HULU = 'HULU';
	
	public static function getAdditionalValues()
	{
		return array(
			'HULU' => self::HULU,
		);
	}
}
