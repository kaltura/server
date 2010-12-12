<?php
interface IDistributionEngineSubmit
{
	/**
	 * sends media to external system.
	 * @return KalturaBatchJobStatus
	 */
	public function submit(KalturaDistributionJobData $data);
}