<?php

/**
 * Subclass for representing a row from the 'scheduler_worker' table.
 *
 * 
 *
 * @package lib.model
 */ 
class SchedulerWorker extends BaseSchedulerWorker
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
	
	public function getLockedJobs()
	{
		$c = new Criteria();
		$c->add(BatchJobPeer::BATCH_INDEX, null, Criteria::ISNOTNULL);
		$c->add(BatchJobPeer::SCHEDULER_ID, $this->scheduler_configured_id);
		$c->add(BatchJobPeer::WORKER_ID, $this->configured_id);
		
		return BatchJobPeer::doSelect($c, myDbHelper::getConnection(myDbHelper::DB_HELPER_CONN_PROPEL2));
	}
	
	public function getConfigs()
	{
		$c = new Criteria();
		$c->clearSelectColumns();
		$c->addSelectColumn('MAX(' . SchedulerConfigPeer::ID . ')');
		$c->addGroupByColumn(SchedulerConfigPeer::VARIABLE);
		$c->addGroupByColumn(SchedulerConfigPeer::VARIABLE_PART);
		$c->addAscendingOrderByColumn(SchedulerConfigPeer::VARIABLE);
		$c->add(SchedulerConfigPeer::WORKER_ID, $this->id);
		
		$rs = SchedulerConfigPeer::doSelectStmt($c, myDbHelper::getConnection(myDbHelper::DB_HELPER_CONN_PROPEL2));

//		$configIds = array();
		$configIds = $rs->fetchAll(PDO::FETCH_COLUMN);
		
//		while ($rs->next())
//			$configIds[] = $rs->getInt(1);
		
		return SchedulerConfigPeer::retrieveByPKs($configIds, myDbHelper::getConnection(myDbHelper::DB_HELPER_CONN_PROPEL2));
	} 
}
