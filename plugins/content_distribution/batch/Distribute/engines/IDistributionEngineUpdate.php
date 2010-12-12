<?php
interface IDistributionEngineUpdate
{
	/**
	 * updates media or metadata.
	 * @return KalturaBatchJobStatus
	 */
	public function update(KalturaDistributionJobData $data);
}