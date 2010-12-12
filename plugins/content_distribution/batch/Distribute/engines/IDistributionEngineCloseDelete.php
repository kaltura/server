<?php
interface IDistributionEngineCloseDelete
{
	/**
	 * check for deletion closure in case the deletion is asynchronous.
	 * @return KalturaBatchJobStatus
	 */
	public function closeDelete(KalturaDistributionJobData $data);
}