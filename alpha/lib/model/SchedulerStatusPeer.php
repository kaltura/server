<?php

/**
 * Subclass for performing query and update operations on the 'scheduler_status' table.
 *
 * 
 *
 * @package lib.model
 */ 
class SchedulerStatusPeer extends BaseSchedulerStatusPeer
{
	public static function deleteBySchedulerConfigId($schedulerId)
	{
		$criteria = new Criteria();
		$criteria->add(SchedulerStatusPeer::SCHEDULER_CONFIGURED_ID, $schedulerId);

		SchedulerStatusPeer::doDelete($criteria);
	}
}
