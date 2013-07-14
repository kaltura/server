<?php
/**
 * @package plugins.contentDistribution 
 * @subpackage Scheduler.Distribute
 */
interface IDistributionEngineEnable extends IDistributionEngineUpdate
{
	/**
	 * enables the package.
	 * @param KalturaDistributionEnableJobData $data
	 * @return bool true if finished, false if will be finished asynchronously
	 */
	public function enable(KalturaDistributionEnableJobData $data);
}