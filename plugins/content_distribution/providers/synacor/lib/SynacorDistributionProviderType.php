<?php
/**
 * @package plugins.synacorDistribution
 * @subpackage lib
 */
class SynacorDistributionProviderType implements IKalturaPluginEnum, DistributionProviderType
{
	const SYNACOR = 'SYNACOR';
	
	public static function getAdditionalValues()
	{
		return array(
			'SYNACOR' => self::SYNACOR,
		);
	}
}
