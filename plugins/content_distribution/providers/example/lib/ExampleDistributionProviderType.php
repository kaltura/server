<?php
/**
 * @package plugins.exampleDistribution
 * @subpackage lib
 */
class ExampleDistributionProviderType implements IKalturaPluginEnum, DistributionProviderType
{
	const EXAMPLE = 'EXAMPLE';
	
	public static function getAdditionalValues()
	{
		return array(
			'EXAMPLE' => self::EXAMPLE,
		);
	}
}
