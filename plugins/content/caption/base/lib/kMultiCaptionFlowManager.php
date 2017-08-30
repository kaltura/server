<?php
class kMultiCaptionFlowManager implements kBatchJobStatusEventConsumer, kObjectAddedEventConsumer, kObjectChangedEventConsumer, kObjectReplacedEventConsumer
{
	
	/* (non-PHPdoc)
	 * @see kBatchJobStatusEventConsumer::shouldConsumeJobStatusEvent()
	 */
	public function shouldConsumeJobStatusEvent(BatchJob $dbBatchJob)
	{
		if($dbBatchJob->getStatus() == BatchJob::BATCHJOB_STATUS_FAILED)
		{
			$parseMultiBatchJobType = CaptionPlugin::getBatchJobTypeCoreValue(ParseMultiLanguageCaptionAssetBatchType::PARSE_MULTI_LANGUAGE_CAPTION_ASSET);
			if ($dbBatchJob->getJobType() == $parseMultiBatchJobType)
				return true;
		}
		return false;
	}

    /* (non-PHPdoc)
    * @see kObjectReplacedEventConsumer::shouldConsumeReplacedEvent()
    */
    public function shouldConsumeReplacedEvent(BaseObject $object)
    {
        if($object instanceof entry) {
            return true;
        }
        return false;
    }
	
	/* (non-PHPdoc)
	 * @see kBatchJobStatusEventConsumer::updatedJob()
	 */
	public function updatedJob(BatchJob $dbBatchJob)
	{	
		try 
		{
			$dbBatchJob = $this->updatedParseMulti($dbBatchJob, $dbBatchJob->getData());
		}
		catch(Exception $e)
		{
			KalturaLog::err('Failed to process updatedJob - '.$e->getMessage());
		}
		return true;					
	}

	private function updatedParseMulti($dbBatchJob,$data)
	{
		$captionId = $data->getMultiLanaguageCaptionAssetId();
		$captionAsset = assetPeer::retrieveById($captionId);
		$captionAsset->setStatus(asset::ASSET_STATUS_ERROR);
		$captionAsset->save();
	}
	
	/* (non-PHPdoc)
 	 * @see kObjectAddedEventConsumer::shouldConsumeAddedEvent()
 	 */
	public function shouldConsumeAddedEvent(BaseObject $object)
	{
		if($object instanceof entry && $object->getReplacedEntryId() && $object->getIsTemporary() && !$object->getIsTrimmed())
			return true;
		
		return false;
	}
	
	/* (non-PHPdoc)
 	 * @see kObjectAddedEventConsumer::objectAdded()
 	 */
	public function objectAdded(BaseObject $object, BatchJob $raisedJob = null)
	{
		if($object instanceof entry && $object->getReplacedEntryId() && $object->getIsTemporary() && !$object->getIsTrimmed())
		{
            $this->copyUpdatedCaptionsToEntry($object);
		}
		
		return true;
	}


    /* (non-PHPdoc)
	 * @see kObjectReplacedEventConsumer::objectReplaced()
	*/
    public function objectReplaced(BaseObject $object, BaseObject $replacingObject, BatchJob $raisedJob = null) {
        $clipAttributes = self::getClipAttributesFromEntry($replacingObject);
        //replacement as a result of trimming
        if (!is_null($clipAttributes)) {
            kEventsManager::setForceDeferredEvents(true);
            $c = new Criteria();
            $c->add(assetPeer::ENTRY_ID, $object->getId());
            $types = array(CaptionPlugin::getAssetTypeCoreValue(CaptionAssetType::CAPTION));
            if(count($types))
                $c->add(assetPeer::TYPE, $types, Criteria::IN);
            $this->deleteCaptions($c);
            //copy captions from replacement entry
            $replacementCaptions = assetPeer::retrieveByEntryId($replacingObject->getId(), array(CaptionPlugin::getAssetTypeCoreValue(CaptionAssetType::CAPTION)));
            foreach ($replacementCaptions as $captionAsset) {
                $newCaptionAsset = $captionAsset->copyToEntry($object->getId());
                $newCaptionAsset->save();
            }
            kEventsManager::flushEvents();
        }
        return true;
    }


    protected function deleteCaptions($c)
    {
        CuePointPeer::setUseCriteriaFilter(false);
        $captions = assetPeer::doSelect($c);
        $update = new Criteria();
        $update->add(assetPeer::STATUS, KalturaCaptionAssetStatus::DELETED);

        $con = Propel::getConnection(myDbHelper::DB_HELPER_CONN_MASTER);
        BasePeer::doUpdate($c, $update, $con);
        CuePointPeer::setUseCriteriaFilter(true);
        foreach($captions as $caption)
        {
            $caption->setStatus(KalturaCaptionAssetStatus::DELETED);
            KalturaLog::info("Deleted caption asset: [{$caption->getId()}]");
            kEventsManager::raiseEvent(new kObjectDeletedEvent($caption));
        }
    }

    /* (non-PHPdoc)
   * @see kObjectChangedEventConsumer::shouldConsumeChangedEvent()
   */
    public function shouldConsumeChangedEvent(BaseObject $object, array $modifiedColumns)
    {
        if (myEntryUtils::wasEntryClipped($object, $modifiedColumns)) {
            return true;
        }
        return false;
    }

    /* (non-PHPdoc)
	 * @see kObjectChangedEventConsumer::objectChanged()
	 */
    public function objectChanged(BaseObject $object, array $modifiedColumns)
    {
        if (myEntryUtils::wasEntryClipped($object, $modifiedColumns))
        {
            $this->copyUpdatedCaptionsToEntry($object);
        }
        return true;
    }


    /**
     *
     * @param entry $destEntry new entry to copy and adjust captions from root entry to
     */
    protected function copyUpdatedCaptionsToEntry(entry $destEntry)
    {
        $jobData = new kCopyCaptionsJobData();
        $jobData->setEntryId($destEntry->getId());

        //regular replacement
        if(!$destEntry->getIsTrimmed() && $destEntry->getReplacedEntryId()){
            $sourceEntryId = $destEntry->getReplacedEntryId();
            $sourceEntry = entryPeer::retrieveByPK($sourceEntryId);
            if(!$sourceEntry)
            {
                KalturaLog::debug("Didn't copy captions for entry [{$destEntry->getId()}] because source entry [" . $sourceEntryId . "] wasn't found");
                return;
            }
            $jobData->setOffset(0);
            $jobData->setDuration($sourceEntry->getLengthInMsecs());
        }
        else { //trim or clip
            $clipAttributes = self::getClipAttributesFromEntry($destEntry);
            if (!is_null($clipAttributes)) {
                $sourceEntry = entryPeer::retrieveByPK($destEntry->getSourceEntryId());
                if (is_null($sourceEntry)) {
                    KalturaLog::info("Didn't copy captions for entry [{$destEntry->getId()}] because source entry [" . $destEntry->getSourceEntryId() . "] wasn't found");
                    return;
                }

                $jobData->setOffset($clipAttributes->getOffset());
                $jobData->setDuration($clipAttributes->getDuration());
            }
        }

        $jobData->setSourceEntryId($sourceEntry->getId());

        $batchJob = new BatchJob();
        $batchJob->setEntryId($destEntry->getId());
        $batchJob->setPartnerId($destEntry->getPartnerId());

        kJobsManager::addJob($batchJob, $jobData, BatchJobType::COPY_CAPTIONS);
        return;
    }

    /**
     * @param BaseObject entry to check
     * @return kClipAttributes|null
     */
    protected static function getClipAttributesFromEntry(BaseObject $object) {
        if ($object instanceof entry) {
            $operationAttributes = $object->getOperationAttributes();
            if (!is_null($operationAttributes) && count($operationAttributes) > 0) {
                $clipAttributes = reset($operationAttributes);
                if ($clipAttributes instanceof kClipAttributes) {
                    return $clipAttributes;
                }
            }
        }
        return null;
    }
}
