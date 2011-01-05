<?php
/**
 * @package api
 * @subpackage enum
 */
class MsnDistributionProviderType implements IKalturaPluginEnum, DistributionProviderType
{
	const MSN = 'MSN';
	
	public static function getAdditionalValues()
	{
		return array(
			'MSN' => self::MSN,
		);
	}
}
