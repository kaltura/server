<?php
/**
 * @package api
 * @subpackage enum
 */
class ContentDistributionFileSyncObjectType implements IKalturaPluginEnum, FileSyncObjectType
{
	const GENERIC_DISTRIBUTION_ACTION = 'GenericDistributionAction';
	const ENTRY_DISTRIBUTION = 'EntryDistribution';
	const DISTRIBUTION_PROFILE = 'DistributionProfile';
	
	public static function getAdditionalValues()
	{
		return array(
			'GENERIC_DISTRIBUTION_ACTION' => self::GENERIC_DISTRIBUTION_ACTION,
			'ENTRY_DISTRIBUTION' => self::ENTRY_DISTRIBUTION,
			'DISTRIBUTION_PROFILE' => self::DISTRIBUTION_PROFILE,
		);
	}
}
