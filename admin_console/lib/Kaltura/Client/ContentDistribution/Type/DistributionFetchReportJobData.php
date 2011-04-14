<?php
/**
 * @package Admin
 * @subpackage Client
 */
class Kaltura_Client_ContentDistribution_Type_DistributionFetchReportJobData extends Kaltura_Client_ContentDistribution_Type_DistributionJobData
{
	public function getKalturaObjectType()
	{
		return 'KalturaDistributionFetchReportJobData';
	}
	
	/**
	 * 
	 *
	 * @var int
	 */
	public $plays = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $views = null;


}

