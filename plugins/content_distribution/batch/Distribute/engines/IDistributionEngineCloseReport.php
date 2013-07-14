<?php
/**
 * @package plugins.contentDistribution 
 * @subpackage Scheduler.Distribute
 */
interface IDistributionEngineCloseReport extends IDistributionEngine
{
	/**
	 * check for report fetching closure in case the fething is asynchronous.
	 * @param KalturaDistributionFetchReportJobData $data
	 * @return bool true if finished, false if will be finished asynchronously
	 */
	public function closeReport(KalturaDistributionFetchReportJobData $data);
}