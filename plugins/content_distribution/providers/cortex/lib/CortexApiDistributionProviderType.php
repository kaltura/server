<?php
/**
 * @package plugins.cortexApiDistribution
 * @subpackage lib
 */
class CortexApiDistributionProviderType implements IKalturaPluginEnum, DistributionProviderType
{
	const CORTEX_API = 'CORTEX_API';
	
	public static function getAdditionalValues()
	{
		return array(
			'CORTEX_API' => self::CORTEX_API,
		);
	}
	
	/**
	 * @return array
	 */
	public static function getAdditionalDescriptions()
	{
		return array();
	}
}
