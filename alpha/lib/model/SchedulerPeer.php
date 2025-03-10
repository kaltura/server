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
	public static function deleteBySchedulerConfigId($schedulerId, $schedulerConfiguredId)
	{
		$criteria = new Criteria();
		$criteria->add(SchedulerPeer::CONFIGURED_ID, $schedulerConfiguredId);
		SchedulerPeer::doDelete($criteria);
		
		SchedulerWorkerPeer::deleteBySchedulerConfigId($schedulerConfiguredId);
		SchedulerConfigPeer::deleteBySchedulerId($schedulerId);
		SchedulerStatusPeer::deleteBySchedulerId($schedulerId);
		ControlPanelCommandPeer::deleteBySchedulerConfigId($schedulerConfiguredId);
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
			throw new kCoreException("Could not find scheduler id for scheduler for host $hostname.");

		return $result[0]->getConfiguredId();
	}
	
	public static function getConfiguredIdBySchedulerId($schedulerId)
	{
		$c = new Criteria();
		$c->add(SchedulerPeer::CONFIGURED_ID, $schedulerId);
		return SchedulerPeer::doSelectOne($c);
	}

	public static function getScheudlerByHostName($hostname)
	{
		$c = new Criteria();
		$c->add(SchedulerPeer::HOST, $hostname);
		return SchedulerPeer::doSelectOne($c);
	}
	
	public static function getCacheInvalidationKeys()
	{
		return array(array("scheduler:configuredId=%s", self::CONFIGURED_ID));		
	}
}
