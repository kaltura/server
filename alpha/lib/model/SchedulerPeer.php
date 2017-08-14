<?php

/**
 * Subclass for performing query and update operations on the 'scheduler' table.
 *
 * 
 *
 * @package Core
 * @subpackage model
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
	
	public static function getConfiguredIdByHostName($hostname)
	{
		$c = new Criteria();
		$c->add(SchedulerPeer::HOST, $hostname);
		$result = SchedulerPeer::doSelect( $c);
		if (!$result)
			throw new kCoreException("Could not find scheduler for host $hostname");

		if ( count($result)> 1 )
			throw new kCoreException("More than one result for host $hostname");

		if (!$result[0]->getConfiguredId())
			throw new kCoreException("Could not find scheduler id for scheduler.");

		return $result[0]->getConfiguredId();
	}
}
