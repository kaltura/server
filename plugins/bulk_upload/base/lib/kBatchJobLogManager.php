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
                
        $batchJobLog = $this->copyBatchJobToLog($object, $batchJobLog);
        
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

	protected function copyBatchJobToLog($batchJob, $batchJobLog)
	{
	    $batchJobLog->setAbort($batchJob->getAbort());
	    $batchJobLog->setBatchIndex($batchJob->getBatchIndex());
	    $batchJobLog->setBulkJobId($batchJob->getBulkJobId());
	    $batchJobLog->setCheckAgainTimeout($batchJob->getCheckAgainTimeout());
	    $batchJobLog->setCreatedAt($batchJob->getCreatedAt());
	    $batchJobLog->setCreatedBy($batchJob->getCreatedBy());
	    $batchJobLog->setData($batchJob->getData());
	    $batchJobLog->setDc($batchJob->getDc());
	    $batchJobLog->setDeletedAt($batchJob->getDeletedAt());
	    $batchJobLog->setDescription($batchJob->getDescription());
	    $batchJobLog->setDuplicationKey($batchJob->getDuplicationKey());
	    $batchJobLog->setEntryId($batchJob->getEntryId());
	    $batchJobLog->setErrNumber($batchJob->getErrNumber());
	    $batchJobLog->setErrType($batchJob->getErrType());
	    $batchJobLog->setExecutionAttempts($batchJob->getExecutionAttempts());
	    $batchJobLog->setFileSize($batchJob->getFileSize());
	    $batchJobLog->setFinishTime($batchJob->getFinishTime());
	    $batchJobLog->setJobId($batchJob->getId());
	    $batchJobLog->setJobSubType($batchJob->getJobSubType());
	    $batchJobLog->setJobType($batchJob->getJobType());
	    $batchJobLog->setLastSchedulerId($batchJob->getLastSchedulerId());
	    $batchJobLog->setLastWorkerId($batchJob->getLastWorkerId());
	    $batchJobLog->setLastWorkerRemote($batchJob->getLastWorkerRemote());
	    $batchJobLog->setLockVersion($batchJob->getLockVersion());
	    $batchJobLog->setMessage($batchJob->getMessage());
	    $batchJobLog->setOnStressDivertTo($batchJob->getOnStressDivertTo());
	    $batchJobLog->setParentJobId($batchJob->getParentJobId());
	    $batchJobLog->setPartnerId($batchJob->getPartnerId());
	    $batchJobLog->setPrimaryKey($batchJob->getPrimaryKey());
	    $batchJobLog->setPriority($batchJob->getPriority());
	    $batchJobLog->setProcessorExpiration($batchJob->getProcessorExpiration());
	    $batchJobLog->setProgress($batchJob->getProgress());
	    $batchJobLog->setQueueTime($batchJob->getQueueTime());
	    $batchJobLog->setRootJobId($batchJob->getRootJobId());
	    $batchJobLog->setSchedulerId($batchJob->getSchedulerId());
	    $batchJobLog->setJobStatus($batchJob->getStatus());
	    $batchJobLog->setSubpId($batchJob->getSubpId());
	    $batchJobLog->setTwinJobId($batchJob->getTwinJobId());
	    $batchJobLog->setUpdatedAt($batchJob->getUpdatedAt());
	    $batchJobLog->setUpdatedBy($batchJob->getUpdatedBy());
	    $batchJobLog->setUpdatesCount($batchJob->getUpdatesCount());
	    $batchJobLog->setWorkerId($batchJob->getWorkerId());
	    $batchJobLog->setWorkGroupId($batchJob->getWorkGroupId());
	    //set param_1 for the $batchJobLog
	    $batchJobData = $batchJob->getData();
	    /* @var $batchJobData kBulkUploadJobData */
	    $batchJobLog->setParam1($batchJobData->getBulkUploadObjectType());

		return $batchJobLog;
	}

    
}