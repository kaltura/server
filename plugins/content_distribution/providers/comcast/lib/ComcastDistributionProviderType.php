<?php
/**
 * @package plugins.comcastDistribution
 * @subpackage lib
 */
class ComcastDistributionProviderType implements IKalturaPluginEnum, DistributionProviderType
{
	const COMCAST = 'COMCAST';
	
	public static function getAdditionalValues()
	{
		return array(
			'COMCAST' => self::COMCAST,
		);
	}
}
