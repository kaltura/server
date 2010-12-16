<?php
interface IDistributionEngineSubmit extends IDistributionEngine
{
	/**
	 * sends media to external system.
	 * @param KalturaDistributionSubmitJobData $data
	 * @return bool true if finished, false if will be finished asynchronously
	 */
	public function submit(KalturaDistributionSubmitJobData $data);
}