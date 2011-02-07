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
	public static function deleteBySchedulerConfigId($schedulerId)
	{
		$criteria = new Criteria();
		$criteria->add(SchedulerWorkerPeer::SCHEDULER_CONFIGURED_ID, $schedulerId);

		SchedulerWorkerPeer::doDelete($criteria);
	}
}
