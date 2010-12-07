<?php
interface IDistributionEngineCloseReport
{
	/**
	 * check for report fetching closure in case the fething is asynchronous.
	 * @return KalturaBatchJobStatus
	 */
	public function closeReport();
}