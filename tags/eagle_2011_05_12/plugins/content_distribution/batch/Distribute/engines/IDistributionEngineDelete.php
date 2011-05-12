<?php
interface IDistributionEngineDelete extends IDistributionEngine
{
	/**
	 * removes media.
	 * @param KalturaDistributionDeleteJobData $data
	 * @return bool true if finished, false if will be finished asynchronously
	 */
	public function delete(KalturaDistributionDeleteJobData $data);
}