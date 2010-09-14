<?php

/**
 * Subclass for representing a row from the 'scheduler' table.
 *
 * 
 *
 * @package lib.model
 */ 
class Scheduler extends BaseScheduler
{

	public function getStatuses()
	{
		$statuses = parent::getStatuses();
		if(is_null($statuses))
			return array();
			
		return unserialize($statuses);
	}
	
	public function setStatuses($v)
	{
		if(!is_array($v))
			$v = array();
			
		parent::setStatuses(serialize($v));
	} 
	
	public function setStatus($type, $v)
	{
		$this->setLastStatus(time());
		
		$statuses = $this->getStatuses();
		$statuses[$type] = $v;
			
		$this->setStatuses($statuses);
	} 
	
	public function getStatus($type)
	{
		$statuses = $this->getStatuses();
		return @$statuses[$type];
	} 
	
	public function getWorkers()
	{
		$c = new Criteria();
		$c->add(SchedulerWorkerPeer::SCHEDULER_ID, $this->id);
		
		return SchedulerWorkerPeer::doSelect($c, myDbHelper::getConnection(myDbHelper::DB_HELPER_CONN_PROPEL2));
	} 
	
	public function getConfigs()
	{
		$c = new Criteria();
		$c->clearSelectColumns();
		$c->addSelectColumn('MAX(' . SchedulerConfigPeer::ID . ')');
		$c->addGroupByColumn(SchedulerConfigPeer::VARIABLE);
		$c->addGroupByColumn(SchedulerConfigPeer::VARIABLE_PART);
		$c->addAscendingOrderByColumn(SchedulerConfigPeer::VARIABLE);
		$c->add(SchedulerConfigPeer::SCHEDULER_ID, $this->id);
		$c->add(SchedulerConfigPeer::WORKER_ID, null);
		
		$rs = SchedulerConfigPeer::doSelectStmt($c, myDbHelper::getConnection(myDbHelper::DB_HELPER_CONN_PROPEL2));
		$configIds = $rs->fetchAll(PDO::FETCH_COLUMN, 0);
		
		return SchedulerConfigPeer::retrieveByPKs($configIds, myDbHelper::getConnection(myDbHelper::DB_HELPER_CONN_PROPEL2));
	} 
}
