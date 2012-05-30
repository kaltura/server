<?php
class kBatchJobLogManager implements kObjectCreatedEventConsumer, kObjectChangedEventConsumer
{
	/* (non-PHPdoc)
     * @see kObjectChangedEventConsumer::objectChanged()
     */
    public function objectChanged (BaseObject $object, array $modifiedColumns)
    {
        $c = new Criteria();
        $c->addAnd(BatchJobLogPeer::JOB_ID, $object->getId());
        $batchJobLog = BatchJobLogPeer::doSelectOne($c);
        if (!$batchJobLog)
        {
            return;
        }
        
        KalturaLog::info("Handling batch job log object with Id [" . $batchJobLog->getId() ."]");
                
        $batchJobLog = $this->copyModifiedColumns($batchJobLog, $object, $modifiedColumns);
        
        $batchJobLog->save();
    }

	/* (non-PHPdoc)
     * @see kObjectChangedEventConsumer::shouldConsumeChangedEvent()
     */
    public function shouldConsumeChangedEvent (BaseObject $object, array $modifiedColumns)
    {
        if ($object instanceof BatchJob && $object->getJobType() == BatchJobType::BULKUPLOAD)
        {
            return true;
        }
        
        return false;
        
    }

	/* (non-PHPdoc)
     * @see kObjectCreatedEventConsumer::objectCreated()
     */
    public function objectCreated (BaseObject $object)
    {
        /* var $object BatchJob */
        $batchJobLog = new BatchJobLog();
        
        $batchJobLog = $this->copyBatchJobToLog($object, $batchJobLog);
        
        $batchJobLog->save();
    }

	/* (non-PHPdoc)
     * @see kObjectCreatedEventConsumer::shouldConsumeCreatedEvent()
     */
    public function shouldConsumeCreatedEvent (BaseObject $object)
    {
        if ($object instanceof BatchJob && $object->getJobType() == BatchJobType::BULKUPLOAD)
        {
            return true;
        }
        
        return false;
    }

	protected function copyBatchJobToLog(BatchJob $batchJob, BatchJobLog $batchJobLog)
	{
	    $batchJob->copyInto($batchJobLog, true);
	    $batchJobLog->setJobId($batchJob->getId());
	    
		return $batchJobLog;
	}
	
	protected function copyModifiedColumns (BatchJobLog $batchJobLog, BatchJob $batchJob, array $modifiedColumns)
	{
	    foreach ($modifiedColumns as $modifiedColumn)
	    {
	        try 
	        {
    	        $fieldName = BatchJobPeer::translateFieldName($modifiedColumn, BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME);
    	        $fieldPosJob = BatchJobPeer::translateFieldName($modifiedColumn, BasePeer::TYPE_COLNAME, BasePeer::TYPE_NUM);
    	        $fieldPosLog = BatchJobLogPeer::translateFieldName($fieldName, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM);
	        }
	        catch (PropelException $e)
	        {
	            KalturaLog::err("Could not set value for BatchJobLog field $fieldName, exception thrown: ".$e->getMessage());
	        }
	        $batchJobLog->setByPosition($fieldPosLog, $batchJob->getByPosition($fieldPosJob));
	        if ($modifiedColumn == BatchJobPeer::DATA)
	        {
	            //set param_1 for the $batchJobLog
        	    $batchJobData = $batchJob->getData();
        	    /* @var $batchJobData kBulkUploadJobData */
        	    $batchJobLog->setParam1($batchJobData->getBulkUploadObjectType());
	        }
	    }	 

	    return $batchJobLog;
	}
    
}