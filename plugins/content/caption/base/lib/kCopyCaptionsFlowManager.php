<?php
class kCopyCaptionsFlowManager implements  kObjectAddedEventConsumer, kObjectChangedEventConsumer, kObjectReplacedEventConsumer
{
	/* (non-PHPdoc)
  * @see kObjectReplacedEventConsumer::shouldConsumeReplacedEvent()
  */
	public function shouldConsumeReplacedEvent(BaseObject $object)
	{
		if($object instanceof entry)
			return true;

		return false;
	}

	/* (non-PHPdoc)
	 * @see kObjectAddedEventConsumer::shouldConsumeAddedEvent()
	 */
	public function shouldConsumeAddedEvent(BaseObject $object)
	{
		if($object instanceof entry && $object->getReplacedEntryId() && $object->getIsTemporary() && !$object->getTempTrimEntry())
			return true;

		return false;
	}

	/* (non-PHPdoc)
	  * @see kObjectAddedEventConsumer::objectAdded()
	  */
	public function objectAdded(BaseObject $object, BatchJob $raisedJob = null)
	{
		if($object instanceof entry && $object->getReplacedEntryId() && $object->getIsTemporary() && !$object->getTempTrimEntry())
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
		$clipConcatTrimFlow = self::isClipConcatTrimFlow($replacingObject);
		//replacement as a result of trimming
		if (!is_null($clipAttributes) || $clipConcatTrimFlow)
		{
			kEventsManager::setForceDeferredEvents(true);
			$c = new Criteria();
			$c->add(assetPeer::ENTRY_ID, $object->getId());
			$types = array(CaptionPlugin::getAssetTypeCoreValue(CaptionAssetType::CAPTION));
			if(count($types))
				$c->add(assetPeer::TYPE, $types, Criteria::IN);
			$this->deleteCaptions($c);
			//copy captions from replacement entry
			$replacementCaptions = assetPeer::retrieveByEntryId($replacingObject->getId(), array(CaptionPlugin::getAssetTypeCoreValue(CaptionAssetType::CAPTION)));
			foreach ($replacementCaptions as $captionAsset)
			{
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
		if($object instanceof entry)
		{
			if (myEntryUtils::wasEntryClipped($object, $modifiedColumns))
				return true;
		}
		return false;
	}

	/* (non-PHPdoc)
	 * @see kObjectChangedEventConsumer::objectChanged()
	 */
	public function objectChanged(BaseObject $object, array $modifiedColumns)
	{
		if($object instanceof entry)
		{
			if (myEntryUtils::wasEntryClipped($object, $modifiedColumns))
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
		if(!$destEntry->getTempTrimEntry() && $destEntry->getReplacedEntryId()){
			$sourceEntryId = $destEntry->getReplacedEntryId();
			$sourceEntry = entryPeer::retrieveByPK($sourceEntryId);
			if(!$sourceEntry)
			{
				KalturaLog::debug("Didn't copy captions for entry [{$destEntry->getId()}] because source entry [" . $sourceEntryId . "] wasn't found");
				return;
			}
			
			$captionAssets = assetPeer::retrieveByEntryId($sourceEntryId, array(CaptionPlugin::getAssetTypeCoreValue(CaptionAssetType::CAPTION)));
			if(!count($captionAssets))
			{
				KalturaLog::debug("No captions found on source entry [" . $sourceEntryId . "], no need to run copy captions job");
				return;
			}
			$kClipDescriptionArray = array();
			$kClipDescription = new kClipDescription();
			$kClipDescription->setSourceEntryId($sourceEntryId);
			$kClipDescription->setStartTime(0);
			$kClipDescription->setDuration($sourceEntry->getLengthInMsecs());
			$kClipDescriptionArray[] = $kClipDescription;
			$jobData->setFullCopy(true);
		}
		else { //trim or clip
			$clipAttributes = self::getClipAttributesFromEntry($destEntry);
			if (!is_null($clipAttributes))
			{
				$operationAttributes  = $destEntry->getOperationAttributes();
				$sourceEntry = entryPeer::retrieveByPK($destEntry->getSourceEntryId());
				if (is_null($sourceEntry))
				{
					KalturaLog::info("Didn't copy captions for entry [{$destEntry->getId()}] because source entry [" . $destEntry->getSourceEntryId() . "] wasn't found");
					return;
				}
				
				$sourceEntryId = $sourceEntry->getId();
				$captionAssets = assetPeer::retrieveByEntryId($sourceEntryId, array(CaptionPlugin::getAssetTypeCoreValue(CaptionAssetType::CAPTION)));
				if(!count($captionAssets))
				{
					KalturaLog::debug("No captions found on source entry [" . $sourceEntryId . "], no need to run copy captions job");
					return;
				}
				$globalOffset = 0;
				$kClipDescriptionArray = array();
				/** @var kClipAttributes $operationAttribute */
				foreach ($operationAttributes as $operationAttribute)
				{
					$kClipDescription = new kClipDescription();
					if (!$sourceEntryId)
					{
						//if no source entry we will not copy the entry ID. add clip offset to global offset and continue
						$globalOffset = $globalOffset + $operationAttribute->getDuration();
						continue;
					}
					$kClipDescription->setSourceEntryId($sourceEntryId);
					$kClipDescription->setStartTime($operationAttribute->getOffset() ? $operationAttribute->getOffset() : 0);
					$kClipDescription->setDuration($operationAttribute->getDuration() ? $operationAttribute->getDuration() : $sourceEntry->getLengthInMsecs());
					self::setCaptionGlobalOffset($operationAttribute, $globalOffset, $kClipDescription);
					$kClipDescriptionArray[] = $kClipDescription;
					//add clip offset to global offset
					$globalOffset = $globalOffset + $operationAttribute->getDuration();
				}
				$jobData->setClipsDescriptionArray($kClipDescriptionArray);
				$jobData->setFullCopy(false);
			}
		}

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
		if ($object instanceof entry)
		{
			$operationAttributes = $object->getOperationAttributes();
			if (!is_null($operationAttributes) && count($operationAttributes) > 0)
			{
				$clipAttributes = reset($operationAttributes);
				if ($clipAttributes instanceof kClipAttributes)
					return $clipAttributes;
			}
		}
		return null;
	}

	/**
	 * @param BaseObject $object
	 * @return bool
	 */
	protected static function isClipConcatTrimFlow(BaseObject $object ) {
		if ( $object instanceof entry ) {
			return ($object->getFlowType() == EntryFlowType::trim_concat);
		}
		return false;
	}

	/**
	 * @param kClipAttributes $operationAttribute
	 * @param int $globalOffset
	 * @param kClipDescription $kClipDescription
	 */
	private static function setCaptionGlobalOffset($operationAttribute, $globalOffset, $kClipDescription)
	{
		if ($operationAttribute->getGlobalOffsetInDestination() || $operationAttribute->getGlobalOffsetInDestination() === 0) {
			$kClipDescription->setOffsetInDestination($operationAttribute->getGlobalOffsetInDestination());
		} else {
			$kClipDescription->setOffsetInDestination($globalOffset);
		}
	}

}


