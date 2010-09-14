<?php

/**
 * Subclass for performing query and update operations on the 'scheduler_config' table.
 *
 * 
 *
 * @package lib.model
 */ 
class SchedulerConfigPeer extends BaseSchedulerConfigPeer
{
	public static function deleteBySchedulerConfigId($schedulerId)
	{
		$criteria = new Criteria();
		$criteria->add(SchedulerConfigPeer::SCHEDULER_CONFIGURED_ID, $schedulerId);

		SchedulerConfigPeer::doDelete($criteria);
	}
}
