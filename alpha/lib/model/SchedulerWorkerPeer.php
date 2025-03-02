<?php

/**
 * Subclass for performing query and update operations on the 'scheduler_worker' table.
 *
 * 
 *
 * @package Core
 * @subpackage model
 */ 
class SchedulerWorkerPeer extends BaseSchedulerWorkerPeer
{
	public static function deleteBySchedulerConfigId($schedulerConfiguredId)
	{
		$criteria = new Criteria();
		$criteria->add(SchedulerWorkerPeer::SCHEDULER_CONFIGURED_ID, $schedulerConfiguredId);

		SchedulerWorkerPeer::doDelete($criteria);
	}
	public static function getCacheInvalidationKeys()
	{
		return array(array("schedulerWorker:schedulerConfiguredId=%s", self::SCHEDULER_CONFIGURED_ID));		
	}
}
