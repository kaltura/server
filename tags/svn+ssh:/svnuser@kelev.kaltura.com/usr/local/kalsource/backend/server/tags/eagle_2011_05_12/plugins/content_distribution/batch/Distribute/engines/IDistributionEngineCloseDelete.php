<?php
interface IDistributionEngineCloseDelete extends IDistributionEngine
{
	/**
	 * check for deletion closure in case the deletion is asynchronous.
	 * @param KalturaDistributionDeleteJobData $data
	 * @return bool true if finished, false if will be finished asynchronously
	 */
	public function closeDelete(KalturaDistributionDeleteJobData $data);
}