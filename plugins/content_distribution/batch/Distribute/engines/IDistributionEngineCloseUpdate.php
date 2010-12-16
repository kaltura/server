<?php
interface IDistributionEngineCloseUpdate extends IDistributionEngine
{
	/**
	 * check for update closure in case the update is asynchronous.
	 * @param KalturaDistributionUpdateJobData $data
	 * @return bool true if finished, false if will be finished asynchronously
	 */
	public function closeUpdate(KalturaDistributionUpdateJobData $data);
}