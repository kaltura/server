<?php
/**
 * @package plugins.verizonVcastDistribution
 * @subpackage lib
 */
class VerizonVcastDistributionProviderType implements IKalturaPluginEnum, DistributionProviderType
{
	const VERIZON_VCAST = 'VERIZON_VCAST';
	
	public static function getAdditionalValues()
	{
		return array(
			'VERIZON_VCAST' => self::VERIZON_VCAST,
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
