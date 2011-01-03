<?php
/**
 * @package api
 * @subpackage enum
 */
class ContentDistributionBatchJobType implements IKalturaPluginEnum, BatchJobType
{
	const DISTRIBUTION_SUBMIT = 'DistributionSubmit';
	const DISTRIBUTION_UPDATE = 'DistributionUpdate';
	const DISTRIBUTION_DELETE = 'DistributionDelete';
	const DISTRIBUTION_FETCH_REPORT = 'DistributionFetchReport';
	const DISTRIBUTION_SYNC = 'DistributionSync';
	
	public static function getAdditionalValues()
	{
		return array(
			'DISTRIBUTION_SUBMIT' => self::DISTRIBUTION_SUBMIT,
			'DISTRIBUTION_UPDATE' => self::DISTRIBUTION_UPDATE,
			'DISTRIBUTION_DELETE' => self::DISTRIBUTION_DELETE,
			'DISTRIBUTION_FETCH_REPORT' => self::DISTRIBUTION_FETCH_REPORT,
			'DISTRIBUTION_SYNC' => self::DISTRIBUTION_SYNC,
		);
	}
}
