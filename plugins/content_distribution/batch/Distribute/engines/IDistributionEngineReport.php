<?php
interface IDistributionEngineReport
{
	/**
	 * retrieves statistics.
	 * @return KalturaBatchJobStatus
	 */
	public function fetchReport();
}