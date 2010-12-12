<?php
interface IDistributionEngineReport
{
	/**
	 * retrieves statistics.
	 * @param KalturaDistributionFetchReportJobData $data
	 * @return bool true if finished, false if will be finished asynchronously
	 */
	public function fetchReport(KalturaDistributionFetchReportJobData $data);
}