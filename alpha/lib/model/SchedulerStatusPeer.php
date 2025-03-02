<?php

/**
 * Subclass for performing query and update operations on the 'scheduler_status' table.
 *
 * 
 *
 * @package Core
 * @subpackage model
 */ 
class SchedulerStatusPeer extends BaseSchedulerStatusPeer
{
	public static function deleteBySchedulerId($schedulerId)
	{
		$criteria = new Criteria();
		$criteria->add(SchedulerStatusPeer::SCHEDULER_ID, $schedulerId);

		SchedulerStatusPeer::doDelete($criteria);
	}
}
