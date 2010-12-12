<?php
interface IDistributionEngineCloseUpdate
{
	/**
	 * check for update closure in case the update is asynchronous.
	 * @return KalturaBatchJobStatus
	 */
	public function closeUpdate(KalturaDistributionJobData $data);
}