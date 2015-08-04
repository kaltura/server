<?php


/**
 * Skeleton subclass for representing a row from the 'batch_job_lock' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package Core
 * @subpackage model
 */
class BatchJobLock extends BaseBatchJobLock implements IBaseObject {
	
	public function isRetriesExceeded()
	{
		return ($this->execution_attempts >= BatchJobPeer::getMaxExecutionAttempts($this->job_type));
	}
	
	public function setObjectType($v) {
		$objectType = kPluginableEnumsManager::apiToCore('BatchJobObjectType', $v);
		parent::setObjectType($objectType);
	}
	
	/**
	 * Get the [object_type] column value.
	 *
	 * @return     int
	 */
	public function getObjectType()
	{
		$objectType = parent::getObjectType();
		return kPluginableEnumsManager::coreToApi('BatchJobObjectType', $objectType);
	}
    
} // BatchJobLock
