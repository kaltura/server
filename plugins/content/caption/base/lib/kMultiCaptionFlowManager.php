<?php
class kMultiCaptionFlowManager implements kBatchJobStatusEventConsumer, kObjectAddedEventConsumer
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
		if($object instanceof entry && $object->getReplacedEntryId() && $object->getIsTemporary())
			return true;
		
		return false;
	}
	
	/* (non-PHPdoc)
 	 * @see kObjectAddedEventConsumer::objectAdded()
 	 */
	public function objectAdded(BaseObject $object, BatchJob $raisedJob = null)
	{
		if($object instanceof entry && $object->getReplacedEntryId() && $object->getIsTemporary())
		{
			$this->copyCaptionAssets($object);
		}
		
		return true;
	}
	
	protected function copyCaptionAssets(entry $entry)
	{
		$originalEntryId = $entry->getReplacedEntryId();
		$originalEntry = entryPeer::retrieveByPK($originalEntryId);
		if(!$originalEntry)
		{
			KalturaLog::debug("Original entry with id [$originalEntryId], not found");
			return;
		}
		
		KalturaLog::debug("Original entry id $originalEntryId");
		KalturaLog::debug("Replacing entry id [{$entry->getId()}]");
		
		$captions = assetPeer::retrieveByEntryId($originalEntryId, array(CaptionPlugin::getAssetTypeCoreValue(CaptionAssetType::CAPTION)));
		foreach($captions as $caption)
		{
			/* @var asset $caption */
			$caption->copyToEntry($entry->getId());
		}
	}
}
