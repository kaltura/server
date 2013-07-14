<?php
/**
 * @package plugins.contentDistribution
 * @subpackage lib.enum
 */
class ContentDistributionBatchJobType implements IKalturaPluginEnum, BatchJobType
{
	const DISTRIBUTION_SUBMIT = 'DistributionSubmit';
	const DISTRIBUTION_UPDATE = 'DistributionUpdate';
	const DISTRIBUTION_DELETE = 'DistributionDelete';
	const DISTRIBUTION_FETCH_REPORT = 'DistributionFetchReport';
	const DISTRIBUTION_ENABLE = 'DistributionEnable';
	const DISTRIBUTION_DISABLE = 'DistributionDisable';
	const DISTRIBUTION_SYNC = 'DistributionSync';
	
	public static function getAdditionalValues()
	{
		return array(
			'DISTRIBUTION_SUBMIT' => self::DISTRIBUTION_SUBMIT,
			'DISTRIBUTION_UPDATE' => self::DISTRIBUTION_UPDATE,
			'DISTRIBUTION_DELETE' => self::DISTRIBUTION_DELETE,
			'DISTRIBUTION_FETCH_REPORT' => self::DISTRIBUTION_FETCH_REPORT,
			'DISTRIBUTION_ENABLE' => self::DISTRIBUTION_ENABLE,
			'DISTRIBUTION_DISABLE' => self::DISTRIBUTION_DISABLE,
			'DISTRIBUTION_SYNC' => self::DISTRIBUTION_SYNC,
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
