<?php

class KAMFData
{
	public $pts;
	public $ts;
};

/**
 * @package plugins.cuePoint
 */
class kCuePointManager implements kBatchJobStatusEventConsumer, kObjectDeletedEventConsumer, kObjectChangedEventConsumer, kObjectAddedEventConsumer, kObjectReplacedEventConsumer, kObjectCopiedEventConsumer, kObjectReadyForIndexRelatedObjectsConsumer
{
	const MAX_CUE_POINTS_TO_COPY_TO_VOD = 100;
	const MAX_CUE_POINTS_TO_COPY = 1000;

	/* (non-PHPdoc)
 	 * @see kBatchJobStatusEventConsumer::updatedJob()
 	 */
	public function updatedJob(BatchJob $dbBatchJob)
	{
		if ($dbBatchJob->getJobType() == BatchJobType::CONCAT && $dbBatchJob->getStatus() == BatchJob::BATCHJOB_STATUS_FINISHED
		&& $dbBatchJob->getRootJob() && $dbBatchJob->getRootJob()->getJobType() == BatchJobType::CLIP_CONCAT)
		{
			self::handleConcatAfterClipJobFinished($dbBatchJob->getRootJob() , $dbBatchJob->getRootJob()->getData());
		}
		else if ($dbBatchJob->getJobType() == BatchJobType::CONCAT)
		{
			self::handleConcatJobFinished($dbBatchJob, $dbBatchJob->getData());
		}
		else if ($dbBatchJob->getJobType() == BatchJobType::CONVERT_LIVE_SEGMENT) 
		{
			self::handleConvertLiveSegmentJobFinished($dbBatchJob, $dbBatchJob->getData());
		}
		else if($dbBatchJob->getJobType() == BatchJobType::EXTRACT_MEDIA && $dbBatchJob->getStatus() == BatchJob::BATCHJOB_STATUS_FINISHED)
		{
			self::handleExtractMediaFinished($dbBatchJob, $dbBatchJob->getData());
		}
		return true;
	}

	private function handleConvertLiveSegmentJobFinished(BatchJob $dbBatchJob, kConvertLiveSegmentJobData $data)
	{
		$files = self::getAssetDataFilesArray($data);

		$amfArray = unserialize(file_get_contents($files[0]));
		$recordedVODDurationInMS = $amfArray[0];
		array_shift($amfArray);
		if (!unlink($files[0]))
			KalturaLog::warning("failed to delete file " . $files[0]);

		$amfArray = self::parseAmfArrayAndShift($amfArray, 0);
		$entry = $dbBatchJob->getEntry();
		if (!isset($entry))
		{
			KalturaLog::warning("failed to get entry, not calling copyCuePointsFromLiveToVodEntry");
			return $dbBatchJob;
		}
		self::copyCuePointsFromLiveToVodEntry($entry->getRecordedEntryId(), $recordedVODDurationInMS, $recordedVODDurationInMS, $amfArray);
	}

	private function handleConcatJobFinished(BatchJob $dbBatchJob, kConcatJobData $data)
	{
		if (!$dbBatchJob->getParentJob() || !$dbBatchJob->getParentJob()->getData())
		{
			KalturaLog::warning("failed to get parent job data, not calling copyCuePointsFromLiveToVodEntry");
			return $dbBatchJob;
		}
		$convertJobData = ($dbBatchJob->getParentJob()->getData());

		$files = self::getAssetDataFilesArray($convertJobData);
		$lastFileIndex = $convertJobData->getFileIndex();
		$segmentDuration = 0;
		$amfArray = array();

		foreach($files as $file){
			KalturaLog::debug('file is: ' . $file);

			if (self::getSegmentIndexFromFileName($file) <= $lastFileIndex)
			{
				$arr = unserialize(file_get_contents($files[0]));
				$currentSegmentDuration = $arr[0];
				array_shift($arr);

				$amfArray = array_merge($amfArray, self::parseAmfArrayAndShift($arr, $segmentDuration));
				$segmentDuration += $currentSegmentDuration;
				if (!unlink($file))
					KalturaLog::warning("failed to delete file " . $file);
			}
		}

		$entry = $dbBatchJob->getParentJob()->getEntry();
		if (!isset($entry))
		{
			KalturaLog::warning("failed to get entry, not calling copyCuePointsFromLiveToVodEntry");
			return $dbBatchJob;
		}

		self::copyCuePointsFromLiveToVodEntry( $entry->getRecordedEntryId(), $data->getConcatenatedDuration(), $segmentDuration, $amfArray);
	}

	/**
	 * @param BatchJob $dbBatchJob
	 * @param kClipConcatJobData $data
	 */
	private static function handleConcatAfterClipJobFinished($dbBatchJob, $data)
	{
		/** @var kClipDescription[] $kClipDescriptionArray */
		$kClipDescriptionArray = self::getClipDescriptionFromOperationAttribute($data->getOperationAttributes(), $data->getSourceEntryId());
		KalturaLog::debug("Cue Point Destination Entry ID: [{$data->getDestEntryId()}] and source entry ID: [{$data->getSourceEntryId()}]");
		kJobsManager::addMultiClipCopyCuePointsJob($data->getDestEntryId(), $data->getPartnerId(), $kClipDescriptionArray);
	}

	/**
	 * @param kClipAttributes $operationAttribute
	 * @param int $globalOffset
	 * @param kClipDescription $kClipDescription
	 */
	private static function setCuePointGlobalOffset($operationAttribute, $globalOffset, &$kClipDescription)
	{
		if ($operationAttribute->getGlobalOffsetInDestination() || $operationAttribute->getGlobalOffsetInDestination() === 0) {
			$kClipDescription->setOffsetInDestination($operationAttribute->getGlobalOffsetInDestination());
		} else {
			$kClipDescription->setOffsetInDestination($globalOffset);
		}
	}

	private function handleExtractMediaFinished(BatchJob $dbBatchJob, kExtractMediaJobData $data)
	{
		$entry = entryPeer::retrieveByPKNoFilter($dbBatchJob->getEntryId());
		if(!$entry)
		{
			KalturaLog::warning("Failed to get entry [{$dbBatchJob->getEntryId()}], not calling copyCuePointsFromLiveToVodEntry");
			return $dbBatchJob;
		}
		
		if($entry->getSourceType() == EntrySourceType::KALTURA_RECORDED_LIVE && $data->getDestDataFilePath())
		{
			$replacedEntry = $entry->getReplacedEntryId() ? entryPeer::retrieveByPK($entry->getReplacedEntryId()) : null;
			$liveClipping = ($replacedEntry && $replacedEntry->getFlowType() == EntryFlowType::LIVE_CLIPPING);

			$liveEntryId = $replacedEntry ? $replacedEntry->getRootEntryId() : $entry->getRootEntryId();
			$vodEntryId = $replacedEntry ? $replacedEntry->getId() : $entry->getId();
			$lastSegmentDuration = $replacedEntry ? $entry->getLengthInMsecs() - $replacedEntry->getLengthInMsecs() : $entry->getLengthInMsecs();
			$totalVodDuration = $entry->getLengthInMsecs();
			
			$rawSyncPointDataPath = $data->getDestDataFilePath();
			if(!$rawSyncPointDataPath)
			{
				KalturaLog::debug("SyncPoint data file pat not found, copy live to vod will not execute");
				return $dbBatchJob;
			}
			
			$rawSyncPointInfo = kFile::getFileContent($rawSyncPointDataPath);
			if(!$rawSyncPointInfo)
			{
				KalturaLog::debug("Failed to read sync point info from file [$rawSyncPointDataPath], copy live to vod will not execute");
				return $dbBatchJob;
			}

			$liveEntry = entryPeer::retrieveByPK($liveEntryId);
			if(!$liveEntry || !in_array($liveEntry->getSourceType(), LiveEntry::$kalturaLiveSourceTypes))
			{
				KalturaLog::debug("Live entry with id [$liveEntryId] not found, this should not happen, copy live to vod will not execute");
				return $dbBatchJob;
			}
			
			$syncPointInfoArr = unserialize($rawSyncPointInfo);
			$syncPointIntoToAmfArr = array();
			foreach($syncPointInfoArr as $syncPoint)
			{
				$amfParts = explode(';', $syncPoint);
				$curr = new KAMFData();
				$curr->pts = $amfParts[0];
				$curr->ts = $amfParts[1];
				array_push($syncPointIntoToAmfArr, $curr);
			}
			
			$segmentDrift = 0;
			$syncPointIntoToAmfArr = self::getCurrentSessionSyncPointInfo($syncPointIntoToAmfArr, $totalVodDuration, $lastSegmentDuration);
			if(count($syncPointIntoToAmfArr) > 1)
			{
				$firstAmf = reset($syncPointIntoToAmfArr);
				$lastAmf = end($syncPointIntoToAmfArr);
				$segmentDrift = ($lastAmf->ts - $firstAmf->ts) - ($lastAmf->pts - $firstAmf->pts);
			}
			
			//get last cue points sync, to avoid two sequential jobs working on the same cue points  
			$lastCuePointSyncTime = self::getLatestCuePointSyncTime($liveEntry, reset($syncPointIntoToAmfArr), $lastSegmentDuration, $segmentDrift, $liveClipping);
			
			$liveToVodJobData = new kLiveToVodJobData();
			$liveToVodJobData->setLiveEntryId($liveEntryId);
			$liveToVodJobData->setLastSegmentDuration($lastSegmentDuration);
			$liveToVodJobData->setTotalVodDuration($totalVodDuration);
			$liveToVodJobData->setVodEntryId($vodEntryId);
			$liveToVodJobData->setLastCuePointSyncTime($lastCuePointSyncTime);
			$liveToVodJobData->setLastSegmentDrift($segmentDrift);
			$liveToVodJobData->setAmfArray(json_encode($syncPointIntoToAmfArr));
			
			$liveToVodBatchJob = new BatchJob();
			$liveToVodBatchJob->setEntryId($entry->getId());
			$liveToVodBatchJob->setPartnerId($entry->getPartnerId());

			if ($liveClipping)
				return kJobsManager::addJob($liveToVodBatchJob, $liveToVodJobData, BatchJobType::COPY_CUE_POINTS, CopyCuePointJobType::LIVE_CLIPPING);
			
			kJobsManager::addJob($liveToVodBatchJob, $liveToVodJobData, BatchJobType::LIVE_TO_VOD);
		}
	}
	
	private static function getCurrentSessionSyncPointInfo($syncPointIntoToAmfArr, $totalVodDuration, $lastSegmentDuration)
	{
		$currentSessionAmfArr = array();
		
		$closest = self::getClosestSyncPointPts($totalVodDuration - $lastSegmentDuration, $syncPointIntoToAmfArr);
		if(!$closest)
		{
			KalturaLog::debug("Closest pts not found for current segment");
			return $syncPointIntoToAmfArr;
		}
		
		foreach($syncPointIntoToAmfArr as $amf)
		{
			if($amf->pts < $closest)
				continue;
			
			$amf->pts = $amf->pts - $totalVodDuration + $lastSegmentDuration;
			$currentSessionAmfArr[] = $amf;
		}
		
		return $currentSessionAmfArr;
	}
	
	private static function getClosestSyncPointPts($pts, $amfArr) 
	{
		$closest = null;
		
		foreach ($amfArr as $item) 
		{
			if ($closest === null || abs($pts - $closest->pts) > abs($item->pts - $pts)) 
			{
				$closest = $item;
			}
		}
		
		return $closest->pts;
	}

	// Get an array of strings of the form pts;ts and return an array of KAMFData
	private static function parseAmfArrayAndShift($amfArray, $shift){
		$retArr = array();

		for($i=0; $i < count($amfArray); ++$i){
			$amf = new KAMFData();
			$amfParts = explode(';', $amfArray[$i]);
			$amf->pts = $amfParts[0] + $shift;
			$amf->ts = $amfParts[1];

			KalturaLog::debug('adding AMF to AMFs: ' . print_r($amf, true) . ' extracted from ' . $amfArray[$i]);
			array_push($retArr, $amf);
		}
		return $retArr;
	}
	
	private static function getLatestCuePointSyncTime($liveEntry, $syncPoint, $lastSegmentDuration, $segmentDrift, $liveClipping = false)
	{
		/** @var $liveEntry LiveEntry */
		if ($liveClipping)
		{
			KalturaLog::debug("Live Clipping Flow on [" . $liveEntry->getId() . "] , return lastSyncTime as created Time");
			return $liveEntry->getCreatedAt(null);
		}

		$lastCuePointSyncTime = $liveEntry->getLastCuePointSyncTime();
		if (!$lastCuePointSyncTime)
			$lastCuePointSyncTime = $liveEntry->getCreatedAt(null);

		$currentCuePointSyncTime =  (($syncPoint->ts - $syncPoint->pts) + $lastSegmentDuration + $segmentDrift) / 1000;
		KalturaLog::debug("LastCuePointSyncTime found with value [$lastCuePointSyncTime], new one is set to [$currentCuePointSyncTime]");
		$liveEntry->setLastCuePointSyncTime($currentCuePointSyncTime);
		$liveEntry->save();
		return $lastCuePointSyncTime;
	}

	private function getAssetDataFilesArray(kConvertLiveSegmentJobData $data){

		$amfFilesDir = dirname($data->getDestDataFilePath());
		$pattern = "/{$data->getEntryId()}_{$data->getAssetId()}_{$data->getMediaServerIndex()}_[0-9]*.data/";
		$files = kFile::recursiveDirList($amfFilesDir, true, false, $pattern);
		natsort($files);
		return $files;
	}

	private function getSegmentIndexFromFileName($filePath)
	{
		$lastUnderscore = strrpos($filePath, '_');
		$lastDot = strrpos($filePath, '.');
		return substr($filePath, $lastUnderscore+1, $lastUnderscore-$lastDot-2);
	}

	/* (non-PHPdoc)
 	 * @see kBatchJobStatusEventConsumer::shouldConsumeJobStatusEvent()
 	 */
	public function shouldConsumeJobStatusEvent(BatchJob $dbBatchJob)
	{
		$jobType = $dbBatchJob->getJobType();
		$data = $dbBatchJob->getData();

		// copy cue points only if it's the first file and this is the primary server
		if ($jobType == BatchJobType::CONVERT_LIVE_SEGMENT && $dbBatchJob->getStatus() == BatchJob::BATCHJOB_STATUS_FINISHED &&
			$data->getFileIndex() == 0 && $data->getMediaServerIndex() == EntryServerNodeType::LIVE_PRIMARY)
		{
			$asset = assetPeer::retrieveByIdNoFilter($data->getAssetId());
			if ($asset->hasTag(assetParams::TAG_RECORDING_ANCHOR))
				return true;
		}
		elseif ($jobType == BatchJobType::CONCAT && $dbBatchJob->getStatus() == BatchJob::BATCHJOB_STATUS_FINISHED
		&& $dbBatchJob->getRootJob() && $dbBatchJob->getRootJob()->getJobType() == BatchJobType::CLIP_CONCAT)
		{
			return true;
		}
		elseif ($jobType == BatchJobType::CONCAT && $dbBatchJob->getStatus() == BatchJob::BATCHJOB_STATUS_FINISHED)
		{
			$convertLiveSegmentJobData = $dbBatchJob->getParentJob()->getData();
			$asset = assetPeer::retrieveByIdNoFilter($convertLiveSegmentJobData->getAssetId());
			if ($asset->hasTag(assetParams::TAG_RECORDING_ANCHOR))
				return true;
		}
		elseif ($jobType == BatchJobType::EXTRACT_MEDIA && $dbBatchJob->getStatus() == BatchJob::BATCHJOB_STATUS_FINISHED)
		{
			return true;
		}

		return false;
	}

	/* (non-PHPdoc)
	 * @see kObjectAddedEventConsumer::shouldConsumeAddedEvent()
	 */
	public function shouldConsumeAddedEvent(BaseObject $object)
	{
		if($object instanceof CuePoint)
			return true;
		return false;
	}

	/* (non-PHPdoc)
 	* @see kObjectReadyForIndexRelatedObjectsConsumer::shouldConsumeReadyForIndexRelatedObjectsEvent()
 	*/
	public function shouldConsumeReadyForIndexRelatedObjectsEvent(BaseObject $object)
	{
		if($object instanceof CuePoint)
		{
			return true;
		}
		return false;
	}

	/* (non-PHPdoc)
	 * @see kObjectDeletedEventConsumer::shouldConsumeDeletedEvent()
	 */
	public function shouldConsumeDeletedEvent(BaseObject $object)
	{
		if($object instanceof entry)
			return true;

		if($object instanceof CuePoint)
			return true;

		if($object instanceof QuizUserEntry)
			return true;

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
	 * @see kObjectCopiedEventConsumer::shouldConsumeCopiedEvent()
	 */
	public function shouldConsumeCopiedEvent(BaseObject $fromObject, BaseObject $toObject)
	{
		if($fromObject instanceof entry) {
			return true;
		}
		return false;
	}

	/**
	 * Return a VOD entry (sourceType = RECORDED_LIVE) based on the flavorAsset that is
	 * associated with the given mediaInfo object
	 * @param mediaInfo $mediaInfo
	 * @return entry|null
	 */
	public static function getVodEntryBasedOnMediaInfoFlavorAsset( $mediaInfo )
	{
		if (! ($mediaInfo instanceof mediaInfo) )
		{
			return null;
		}
		$flavorAsset = $mediaInfo->getasset();
		if ( ! $flavorAsset || ! $flavorAsset->hasTag(assetParams::TAG_RECORDING_ANCHOR) )
		{
			return null;
		}
		$vodEntry = $flavorAsset->getentry();
		if ( ! $vodEntry || $vodEntry->getSourceType() != EntrySourceType::RECORDED_LIVE )
		{
			return null;
		}
		return $vodEntry;
	}

	/* (non-PHPdoc)
	 * @see kObjectAddedEventConsumer::objectAdded()
	 */
	public function objectAdded(BaseObject $object, BatchJob $raisedJob = null)
	{
		if($object instanceof CuePoint)
		{
			kEventsManager::raiseEventDeferred(new kObjectReadyForIndexRelatedObjectsEvent($object));
		}
		return true;
	}

	/* (non-PHPdoc)
 	* @see objectReadyForIndexRelatedObjects::objectReadyForIndexRelatedObjects()
 	*/
	public function objectReadyForIndexRelatedObjects(BaseObject $object)
	{
		if($object instanceof CuePoint)
		{
			$this->cuePointIndexEntry($object);
		}
		return true;
	}

	/* (non-PHPdoc)
	 * @see kObjectDeletedEventConsumer::objectDeleted()
	 */
	public function objectDeleted(BaseObject $object, BatchJob $raisedJob = null)
	{
		if($object instanceof entry)
			$this->entryDeleted($object->getId());

		if($object instanceof CuePoint)
			$this->cuePointDeleted($object);

		if($object instanceof QuizUserEntry)
			$this->quizUserEntryDeleted($object);

		return true;
	}

	public function quizUserEntryDeleted(QuizUserEntry $object)
	{
		$c = new Criteria();
		$c->Add(CuePointPeer::ID,$object->getAnswerIds(),Criteria::IN);
		$this->deleteCuePoints($c);
		return true;
	}


	/* (non-PHPdoc)
	 * @see kObjectReplacedEventConsumer::objectReplaced()
	*/
	public function objectReplaced(BaseObject $object, BaseObject $replacingObject, BatchJob $raisedJob = null) {
		//replacement as a result of convertLiveSegmentFinished
		if ( !$replacingObject->getIsTemporary() ) {
			return true;
		}
		$c = new Criteria();
		$c->add(CuePointPeer::ENTRY_ID, $object->getId());
		if ( CuePointPeer::doCount($c) > self::MAX_CUE_POINTS_TO_COPY ) {
			KalturaLog::alert("Can't handle cuePoints after replacement for entry [{$object->getId()}] because cuePoints count exceeded max limit of [" . self::MAX_CUE_POINTS_TO_COPY . "]");
			return true;
		}
		$clipAttributes = self::getClipAttributesFromEntry( $replacingObject );
		$isClipConcatTrimFlow = self::isClipConcatTrimFlow( $replacingObject );
		//replacement as a result of trimming
		if ( !is_null($clipAttributes) || $isClipConcatTrimFlow ) {
			kEventsManager::setForceDeferredEvents( true );
			$this->deleteCuePoints($c);
			//copy cuepoints from replacement entry
			$replacementCuePoints = CuePointPeer::retrieveByEntryId($replacingObject->getId());
			foreach( $replacementCuePoints as $cuePoint ) {
				$cuePoint->setEntryId($object->getId());
				$cuePoint->save();
			}
			kEventsManager::flushEvents();
		} else if (PermissionPeer::isValidForPartner(CuePointPermissionName::REMOVE_CUE_POINTS_WHEN_REPLACING_MEDIA, $object->getPartnerId())) {
			$this->deleteCuePoints($c);
		}
		return true;
	}

	/**
	 * @param entry $entry
	 * @return array
	 */
	private static function getCuePointTypeToClone($entry)
	{
		$listOfEnumIds = array();
		$cue_point_plugin_map = kPluginableEnumsManager::getCoreMap('CuePointType');
		foreach ($cue_point_plugin_map as $dynamic_enum_id => $plugin_name)
		{
			$plugin = kPluginableEnumsManager::getPlugin($plugin_name);
			if($plugin::shouldCloneByProperty($entry)==true) {
				$listOfEnumIds[] = $dynamic_enum_id;
			}
		}
		return $listOfEnumIds;
	}

	/* (non-PHPdoc)
	 * @see kObjectCopiedEventConsumer::objectCopied()
	 */
	public function objectCopied(BaseObject $fromObject, BaseObject $toObject)
	{
		if($fromObject instanceof entry) {
			$c = new KalturaCriteria();
			$c->add(CuePointPeer::ENTRY_ID, $fromObject->getId());
			$c->addAscendingOrderByColumn(CuePointPeer::CREATED_AT);
			$c->setLimit(self::MAX_CUE_POINTS_TO_COPY);
			$cuePointTypes = self::getCuePointTypeToClone($toObject);
			$c->add(CuePointPeer::TYPE,$cuePointTypes,Criteria::IN);
			$cuePoints = CuePointPeer::doSelect($c);
			foreach( $cuePoints as $cuePoint ) {
				$clonedCuePoint = $cuePoint->copyToEntry( $toObject );
				$clonedCuePoint->save();
			}
		}
	}

	/**
	 * @param BaseObject $entry entry to check
	 * @return kClipAttributes|null
	 */
	protected static function getClipAttributesFromEntry( BaseObject $object ) {
		if ( $object instanceof entry ) {
			$operationAtts = $object->getOperationAttributes();
			if ( !is_null($operationAtts) && count($operationAtts) > 0 ) {
				$clipAtts = reset($operationAtts);
				if ($clipAtts instanceof kClipAttributes) {
					return $clipAtts;
				}
			}
		}
		return null;
	}

	/**
	 * @param BaseObject $object
	 * @return bool
	 */
	protected static function isClipConcatTrimFlow(BaseObject $object ) {
		if ( $object instanceof entry )
			return ($object->getFlowType() == EntryFlowType::TRIM_CONCAT);
		return false;
	}

	/**
	 * @param CuePoint $cuePoint
	 */
	protected function cuePointIndexEntry(CuePoint $cuePoint)
	{
		$this->reIndexToSearchEngines($cuePoint);
	}

	/**
	 * @param CuePoint $cuePoint
	 */
	protected function cuePointDeleted(CuePoint $cuePoint)
	{
		if ($cuePoint->getCurrentChildrenCount())
		{
			$c = new Criteria();
			$c->add(CuePointPeer::PARENT_ID, $cuePoint->getId());
			$this->deleteCuePoints($c);
		}

		//re-index cue point on entry if needed
		$this->reIndexToSearchEngines($cuePoint);
	}

	/**
	 * @param int $entryId
	 */
	protected function entryDeleted($entryId)
	{
		$c = new Criteria();
		$c->add(CuePointPeer::ENTRY_ID, $entryId);

		$this->deleteCuePoints($c);
	}

	protected function deleteCuePoints(Criteria $c)
	{
		CuePointPeer::setUseCriteriaFilter(false);
		$cuePoints = CuePointPeer::doSelect($c);
		if (!$cuePoints)
		{
			return;
		}
		$update = new Criteria();
		$update->add(CuePointPeer::STATUS, CuePointStatus::DELETED);
		$update->add(CuePointPeer::UPDATED_AT, time());

		$con = Propel::getConnection(myDbHelper::DB_HELPER_CONN_MASTER);
		BasePeer::doUpdate($c, $update, $con);
		CuePointPeer::setUseCriteriaFilter(true);
		foreach($cuePoints as $cuePoint)
		{
			$cuePoint->setStatus(CuePointStatus::DELETED);
			$cuePoint->indexToSearchIndex();
			kEventsManager::raiseEvent(new kObjectDeletedEvent($cuePoint));
		}
	}

	/**
	 * @param SimpleXMLElement $scene
	 * @param int $partnerId
	 * @param CuePoint $newCuePoint
	 * @return CuePoint
	 */
	public static function parseXml(SimpleXMLElement $scene, $partnerId, CuePoint $newCuePoint = null)
	{
		$cuePoint = null;

		$entryId = (string)$scene['entryId'];
		$entry = entryPeer::retrieveByPK($entryId);
		if(!$entry)
			throw new kCoreException("Entry [$entryId] not found", kCoreException::INVALID_ENTRY_ID);

		if(isset($scene['sceneId']) && $scene['sceneId'])
			$cuePoint = CuePointPeer::retrieveByPK($scene['sceneId']);

		if(!$cuePoint && isset($scene['systemName']) && $scene['systemName'])
			$cuePoint = CuePointPeer::retrieveBySystemName($entryId, $scene['systemName']);

		if(!$cuePoint)
			$cuePoint = $newCuePoint;

		$cuePoint->setPartnerId($partnerId);
		$cuePoint->setStartTime(kXml::timeToInteger($scene->sceneStartTime));

		$tags = array();
		foreach ($scene->tags->children() as $tag)
		{
			$value = "$tag";
			if($value)
				$tags[] = $value;
		}
		$cuePoint->setTags(implode(',', $tags));

		$cuePoint->setEntryId($entryId);
		if(isset($scene['systemName']))
			$cuePoint->setSystemName($scene['systemName']);

		return $cuePoint;
	}

	/**
	 * @param CuePoint $cuePoint
	 * @param SimpleXMLElement $scene
	 * @return SimpleXMLElement the created scene
	 */
	public static function generateCuePointXml(CuePoint $cuePoint, SimpleXMLElement $scene)
	{
		$scene->addAttribute('sceneId', $cuePoint->getId());
		$scene->addAttribute('entryId', $cuePoint->getEntryId());
		if($cuePoint->getSystemName())
			$scene->addAttribute('systemName', kMrssManager::stringToSafeXml($cuePoint->getSystemName()));

		$scene->addChild('sceneStartTime', kXml::integerToTime($cuePoint->getStartTime()));
		if($cuePoint->getPuserId())
			$scene->addChild('userId', kMrssManager::stringToSafeXml($cuePoint->getPuserId()));

		if(trim($cuePoint->getTags(), " \r\n\t"))
		{
			$tags = $scene->addChild('tags');
			foreach(explode(',', $cuePoint->getTags()) as $tag)
				$tags->addChild('tag', kMrssManager::stringToSafeXml($tag));
		}

		return $scene;
	}

	/**
	 * @param CuePoint $cuePoint
	 * @param SimpleXMLElement $scene
	 * @return SimpleXMLElement the created scene
	 */
	public static function syndicateCuePointXml(CuePoint $cuePoint, SimpleXMLElement $scene)
	{
		$scene->addAttribute('sceneId', $cuePoint->getId());
		if($cuePoint->getSystemName())
			$scene->addAttribute('systemName', kMrssManager::stringToSafeXml($cuePoint->getSystemName()));

		$scene->addChild('sceneStartTime', kXml::integerToTime($cuePoint->getStartTime()));
		$scene->addChild('createdAt', ($cuePoint->getCreatedAt(kMrssManager::FORMAT_DATETIME)));
		$scene->addChild('updatedAt', ($cuePoint->getCreatedAt(kMrssManager::FORMAT_DATETIME)));
		if($cuePoint->getPuserId())
			$scene->addChild('userId', kMrssManager::stringToSafeXml($cuePoint->getPuserId()));

		if(trim($cuePoint->getTags(), " \r\n\t"))
		{
			$tags = $scene->addChild('tags');
			foreach(explode(',', $cuePoint->getTags()) as $tag)
				$tags->addChild('tag', kMrssManager::stringToSafeXml($tag));
		}

		return $scene;
	}

	/**
	 * @param string $xmlPath
	 * @param int $partnerId
	 * @return array<CuePoint>
	 */
	public static function addFromXml($xmlPath, $partnerId)
	{
		if(!file_exists($xmlPath))
			throw new kCuePointException("XML file [$xmlPath] not found", kCuePointException::XML_FILE_NOT_FOUND);

		$xml = new KDOMDocument();
		libxml_use_internal_errors(true);
		libxml_clear_errors();
		if(!$xml->load($xmlPath))
		{
			$errorMessage = kXml::getLibXmlErrorDescription(file_get_contents($xmlPath));
			throw new kCuePointException("XML [$xmlPath] is invalid:\n{$errorMessage}", kCuePointException::XML_INVALID);
		}

		$xsdPath = SchemaService::getSchemaPath(CuePointPlugin::getApiValue(CuePointSchemaType::INGEST_API));
		libxml_clear_errors();
		if(!$xml->schemaValidate($xsdPath))
		{
			$errorMessage = kXml::getLibXmlErrorDescription(file_get_contents($xmlPath));
			throw new kCuePointException("XML [$xmlPath] is invalid:\n{$errorMessage}", kCuePointException::XML_INVALID);
		}

		$pluginInstances = KalturaPluginManager::getPluginInstances('IKalturaCuePointXmlParser');
		$scenes = new SimpleXMLElement(file_get_contents($xmlPath));
		$cuePoints = array();

		foreach($scenes as $scene)
		{
			$cuePoint = null;
			foreach($pluginInstances as $pluginInstance)
			{
				$cuePoint = $pluginInstance->parseXml($scene, $partnerId, $cuePoint);
				if($cuePoint)
					$cuePoint->save();
			}

			if($cuePoint && $cuePoint instanceof CuePoint)
			{
				$cuePoints[] = $cuePoint;
			}
		}

		return $cuePoints;
	}

	/**
	 * @param array<CuePoint> $cuePoints
	 * @param SimpleXMLElement $scenes
	 */
	public static function syndicate(array $cuePoints, SimpleXMLElement $scenes)
	{
		$pluginInstances = KalturaPluginManager::getPluginInstances('IKalturaCuePointXmlParser');
		foreach($cuePoints as $cuePoint)
		{
			$scene = null;
			foreach($pluginInstances as $pluginInstance)
				$scene = $pluginInstance->syndicate($cuePoint, $scenes, $scene);
		}
	}

	/**
	 * @param array<CuePoint> $cuePoints
	 * @return string xml
	 */
	public static function generateXml(array $cuePoints)
	{
		$schemaType = CuePointPlugin::getApiValue(CuePointSchemaType::SERVE_API);
		$xsdUrl = "http://" . kConf::get('cdn_host') . "/api_v3/service/schema/action/serve/type/$schemaType";

		$scenes = new SimpleXMLElement('<scenes xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="' . $xsdUrl . '" />');

		$pluginInstances = KalturaPluginManager::getPluginInstances('IKalturaCuePointXmlParser');

		foreach($cuePoints as $cuePoint)
		{
			$scene = null;
			foreach($pluginInstances as $pluginInstance)
				$scene = $pluginInstance->generateXml($cuePoint, $scenes, $scene);
		}

		$xmlContent = $scenes->asXML();

		$xml = new KDOMDocument();
		libxml_use_internal_errors(true);
		libxml_clear_errors();
		if(!$xml->loadXML($xmlContent))
		{
			$errorMessage = kXml::getLibXmlErrorDescription($xmlContent);
			throw new kCuePointException("XML is invalid:\n{$errorMessage}", kCuePointException::XML_INVALID);
		}

		$xsdPath = SchemaService::getSchemaPath($schemaType);
		libxml_clear_errors();
		if(!$xml->schemaValidate($xsdPath))
		{
			$errorMessage = kXml::getLibXmlErrorDescription($xmlContent);
			throw new kCuePointException("XML is invalid:\n{$errorMessage}", kCuePointException::XML_INVALID);
		}

		return $xmlContent;
	}

	/* (non-PHPdoc)
	 * @see kObjectChangedEventConsumer::objectChanged()
	 */
	public function objectChanged(BaseObject $object, array $modifiedColumns)
	{
		if ( self::isPostProcessCuePointsEvent( $object, $modifiedColumns ) )
		{
			self::postProcessCuePoints( $object );
		}

		if(self::shouldReIndexEntry($object, $modifiedColumns))
		{
			$this->reIndexToSearchEngines($object, $modifiedColumns);
		}
		if ( self::wasEntryClipped($object, $modifiedColumns) )
		{
			self::copyCuePointsToClipEntry( $object );
		}
		return true;
	}

	/* (non-PHPdoc)
	 * @see kObjectChangedEventConsumer::shouldConsumeChangedEvent()
	 */
	public function shouldConsumeChangedEvent(BaseObject $object, array $modifiedColumns)
	{
		if ( self::isPostProcessCuePointsEvent($object, $modifiedColumns) )
		{
			return true;
		}
		if( self::shouldReIndexEntry($object, $modifiedColumns) )
		{
			return true;
		}
		if ( self::wasEntryClipped($object, $modifiedColumns) ) {
			return true;
		}
		return false;
	}

	public static function wasEntryClipped(BaseObject $object, array $modifiedColumns)
	{
		if ( ($object instanceof entry)
			&& in_array(entryPeer::CUSTOM_DATA, $modifiedColumns)
			&& $object->isCustomDataModified('operationAttributes')
			&& $object->isCustomDataModified('sourceEntryId') )
		{
			return true;
		}
		return false;
	}

	public static function isPostProcessCuePointsEvent(BaseObject $object, array $modifiedColumns)
	{
		if(	$object instanceof LiveEntry
			&& $object->getRecordStatus() == RecordStatus::DISABLED // If ENABLED, it will be handled at the end of copyCuePointsFromLiveToVodEntry()
			&& !$object->isCurrentlyLive()
		)
		{
			// checking if the live-entry media-server was just unregistered
			$customDataOldValues = $object->getCustomDataOldValues();
			if(isset($customDataOldValues[LiveEntry::CUSTOM_DATA_NAMESPACE_MEDIA_SERVERS]))
			{
				return true;
			}
		}

		return false;
	}

	public static function shouldReIndexEntry(BaseObject $object, array $modifiedColumns)
	{
		if(!($object instanceof CuePoint))
			return false;
		
		/* @var $object CuePoint */
		return $object->shouldReIndexEntry($modifiedColumns) || $object->shouldReIndexEntryToElastic($modifiedColumns);
	}

	public static function postProcessCuePoints( $liveEntry, $cuePointsIds = null )
	{
		if(!$cuePointsIds)
		{
			$cuePointsIds = array();
			
			$c = new Criteria();
			$c->add(CuePointPeer::ENTRY_ID, $liveEntry->getId());
			$c->add(CuePointPeer::STATUS, CuePointStatus::READY);
			$cuePoints = CuePointPeer::doSelect($c);
			foreach($cuePoints as $cuePoint)
			{
				/* @var $cuePoint CuePoint */
				$cuePointsIds[] = $cuePoint->getId();
			}
		}
		
		if(!is_array($cuePointsIds) || !count($cuePointsIds))
		{
			KalturaLog::debug("No cue point to post process for entry [" . $liveEntry->getId() . "]");
			return;
		}
		
		$selectCriteria = new Criteria();
		$selectCriteria->add(CuePointPeer::ID, $cuePointsIds, Criteria::IN);
		
		$updatedAtTime = time();
		$updateCriteria = new Criteria();
		$updateCriteria->add(CuePointPeer::STATUS, CuePointStatus::HANDLED);
		$updateCriteria->add(CuePointPeer::UPDATED_AT, $updatedAtTime);
		
		$con = Propel::getConnection(MetadataPeer::DATABASE_NAME);
		$affectedRows = BasePeer::doUpdate($selectCriteria, $updateCriteria, $con);
		
		if($affectedRows > 0)
		{
			$cuePoints = CuePointPeer::doSelect($selectCriteria);
			foreach($cuePoints as $cuePoint)
			{
				/* @var $cuePoint CuePoint */
				$cuePoint->setUpdatedAt($updatedAtTime);
				$cuePoint->setStatus(CuePointStatus::HANDLED);
				$cuePoint->indexToSearchIndex();
			}
		}
	}

	/**
	 * @param string $vodEntryId
	 */
	public static function copyCuePointsFromLiveToVodEntry( $vodEntryId, $totalVODDuration, $lastSegmentDuration, $amfArray )
	{
		KalturaLog::debug("VOD entry ID: " . $vodEntryId .
			" totalVODDuration: " . $totalVODDuration .
			" lastSegmentDuration " . $lastSegmentDuration .
			" AMFs: " . print_r($amfArray, true));

		if (is_null($vodEntryId) || is_null($totalVODDuration) || is_null($lastSegmentDuration) || is_null($amfArray) || count($amfArray) == 0){
			KalturaLog::warning('bad arguments passed to function. quiting');
			return;
		}

		$vodEntry = entryPeer::retrieveByPK($vodEntryId);
		if ( ! $vodEntry )
		{
			return;
		}
		$liveEntryId = $vodEntry->getRootEntryId();

		/** @var $liveEntry KalturaLiveEntry */
		$liveEntry = entryPeer::retrieveByPK( $liveEntryId );
		if ( ! $liveEntry || ! $liveEntry instanceof LiveEntry )
		{
			KalturaLog::err("Can't find live entry with id [$liveEntryId]");
			return;
 		}

		if (!CuePointPeer::hasReadyCuePointOnEntry($liveEntryId))
			return;

		$jobData = new kLiveToVodJobData();
		$jobData->setVodEntryId($vodEntryId);
		$jobData->setLiveEntryId($liveEntryId);
		$jobData->setTotalVodDuration($totalVODDuration);
		$jobData->setLastSegmentDuration($lastSegmentDuration);
		$jobData->setAmfArray(json_encode($amfArray));
		
		$batchJob = new BatchJob();
		$batchJob->setEntryId($vodEntryId);
		$batchJob->setPartnerId($vodEntry->getPartnerId());
		
		kJobsManager::addJob($batchJob, $jobData, BatchJobType::LIVE_TO_VOD);
		return;
 	}

	public static function reIndexCuePointEntry(CuePoint $cuePoint, $shouldReIndexToSphinx, $shouldReIndexToElastic)
	{
		//index the entry after the cue point was added|deleted
		$entryId = $cuePoint->getEntryId();
		$entry = entryPeer::retrieveByPK($entryId);
		if($entry && $entry->getStatus() != entryStatus::DELETED)
		{
			$entry->setUpdatedAt(time());
			$entry->save();
			if ($shouldReIndexToSphinx)
			{
				$entry->indexToSearchIndex();
			}
			if (!$shouldReIndexToSphinx && $shouldReIndexToElastic) //we don't need to index to elastic if already indexing to sphinx
			{
				$entry->indexToElastic();
			}
		}
	}

	/**
	 *
	 * @param entry $clipEntry new entry to copy and adjust cue points from root entry to
	 */
	public static function copyCuePointsToClipEntry( entry $clipEntry ) {
		$clipAtts =  self::getClipAttributesFromEntry( $clipEntry );
		//if clipConcat flow let batch job copy cue point
		$allowedFlows = array(EntryFlowType::CLIP_CONCAT, EntryFlowType::TRIM_CONCAT);
		if ( $clipAtts &&  !in_array($clipEntry->getFlowType(), $allowedFlows)) {
			$sourceEntry = entryPeer::retrieveByPK( $clipEntry->getSourceEntryId() );
			if ( is_null($sourceEntry) ) {
				KalturaLog::info("Didn't copy cuePoints for entry [{$clipEntry->getId()}] because source entry [" . $clipEntry->getSourceEntryId() . "] wasn't found");
				return;
			}
			$sourceEntryId = $sourceEntry->getID();
			$destEntryId = $clipEntry->getID();
			$kClipDescriptionArray = self::getClipDescriptionFromOperationAttribute(array($clipAtts), $sourceEntryId);
			KalturaLog::debug("Execute Cue-Point copy for clip. From [$sourceEntryId] to [$destEntryId] with operation of " . print_r($kClipDescriptionArray, true));
			return kJobsManager::addMultiClipCopyCuePointsJob($destEntryId, $clipEntry->getPartnerId(), $kClipDescriptionArray);
		}
	}

	protected function reIndexToSearchEngines(CuePoint $cuePoint, array $modifiedColumns = array())
	{
		$shouldReIndexToSphinx = $cuePoint->shouldReIndexEntry($modifiedColumns);
		$shouldReIndexToElastic = $cuePoint->shouldReIndexEntryToElastic($modifiedColumns);
		if($shouldReIndexToSphinx || $shouldReIndexToElastic)
			$this->reIndexCuePointEntry($cuePoint, $shouldReIndexToSphinx, $shouldReIndexToElastic);
	}

	private static function getClipDescriptionFromOperationAttribute($operationAttributes, $sourceEntryId)
	{
		$kClipDescriptionArray = array();
		$globalOffset = 0;
		/** @var kClipAttributes $operationAttribute */
		foreach ($operationAttributes as $operationAttribute)
		{
			$kClipDescription = new kClipDescription();
			if (!$sourceEntryId)
			{
				//if no source entry id we will not copy the entry. add clip offset to global offset and continue
				$globalOffset = $globalOffset + $operationAttribute->getDuration();
				continue;
			}
			$kClipDescription->setSourceEntryId($sourceEntryId);
			$kClipDescription->setStartTime($operationAttribute->getOffset());
			$kClipDescription->setDuration($operationAttribute->getDuration());
			self::setCuePointGlobalOffset($operationAttribute, $globalOffset,$kClipDescription);
			$kClipDescriptionArray[] = $kClipDescription;
			//add clip offset to global offset
			$globalOffset += $operationAttribute->getDuration();
		}
		return $kClipDescriptionArray;
	}
}

