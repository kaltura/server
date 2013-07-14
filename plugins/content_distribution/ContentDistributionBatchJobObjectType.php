<?php
/**
 * @package api
 * @subpackage enum
 */
class ContentDistributionBatchJobObjectType implements IKalturaPluginEnum, BatchJobObjectType
{
	const ENTRY_DISTRIBUTION		= "EntryDistribution";
	
	public static function getAdditionalValues()
	{
		return array(
			'ENTRY_DISTRIBUTION' => self::ENTRY_DISTRIBUTION,
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
