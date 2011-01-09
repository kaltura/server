<?php
/**
 * @package api
 * @subpackage enum
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
