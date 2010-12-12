<?php
interface IDistributionEngineDelete
{
	/**
	 * removes media.
	 * @return KalturaBatchJobStatus
	 */
	public function delete(KalturaDistributionJobData $data);
}