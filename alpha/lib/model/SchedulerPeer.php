<?php

/**
 * Subclass for performing query and update operations on the 'scheduler' table.
 *
 * 
 *
 * @package lib.model
 */ 
class SchedulerPeer extends BaseSchedulerPeer
{
	public static function deleteBySchedulerConfigId($schedulerId)
	{
		$criteria = new Criteria();
		$criteria->add(SchedulerPeer::CONFIGURED_ID, $schedulerId);

		SchedulerPeer::doDelete($criteria);
		
		SchedulerWorkerPeer::deleteBySchedulerConfigId($schedulerId);
		SchedulerConfigPeer::deleteBySchedulerConfigId($schedulerId);
		SchedulerStatusPeer::deleteBySchedulerConfigId($schedulerId);
		ControlPanelCommandPeer::deleteBySchedulerConfigId($schedulerId);
	}
}
