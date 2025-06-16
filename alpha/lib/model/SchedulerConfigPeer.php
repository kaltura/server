<?php

/**
 * Subclass for performing query and update operations on the 'scheduler_config' table.
 *
 * 
 *
 * @package Core
 * @subpackage model
 */ 
class SchedulerConfigPeer extends BaseSchedulerConfigPeer
{
	public static function deleteBySchedulerId($schedulerId)
	{
		$criteria = new Criteria();
		$criteria->add(SchedulerConfigPeer::SCHEDULER_ID, $schedulerId);
		SchedulerConfigPeer::doDelete($criteria);
	}
}
