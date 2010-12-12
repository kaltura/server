<?php
interface IDistributionEngineUpdate
{
	/**
	 * updates media or metadata.
	 * @param KalturaDistributionUpdateJobData $data
	 * @return bool true if finished, false if will be finished asynchronously
	 */
	public function update(KalturaDistributionUpdateJobData $data);
}