<?php
/**
 * @package plugins.cuePoint
 */
class kCuePointManager implements kObjectDeletedEventConsumer, kObjectChangedEventConsumer, kObjectAddedEventConsumer, kObjectCreatedEventConsumer, kObjectReplacedEventConsumer
{
	const MAX_CUE_POINTS_TO_COPY_TO_VOD = 100;
	const MAX_CUE_POINTS_TO_COPY_TO_CLIP = 1000;

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
	 * @see kObjectDeletedEventConsumer::shouldConsumeDeletedEvent()
	 */
	public function shouldConsumeDeletedEvent(BaseObject $object)
	{
		if($object instanceof entry)
			return true;
			
		if($object instanceof CuePoint)
			return true;
			
		return false;
	}
	
	/* (non-PHPdoc)
	 * @see kObjectCreatedEventConsumer::shouldConsumeCreatedEvent()
	*/
	public function shouldConsumeCreatedEvent(BaseObject $object)
	{
		if ( self::getVodEntryBasedOnMediaInfoFlavorAsset($object) )
		{
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
			$this->cuePointAdded($object);
			
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
			
		return true;
	}
	
	/* (non-PHPdoc)
	 * @see kObjectCreatedEventConsumer::objectCreated()
	*/
	public function objectCreated(BaseObject $object)
	{
		self::copyCuePointsFromLiveToVodEntry( $object );

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

		if ( CuePointPeer::doCount($c) > self::MAX_CUE_POINTS_TO_COPY_TO_CLIP ) {
			KalturaLog::alert("Can't handle cuePoints after replacement for entry [{$object->getId()}] because cuePoints count exceeded max limit of [" . self::MAX_CUE_POINTS_TO_COPY_TO_CLIP . "]");
			return true;
		}

		$clipAttributes = self::getClipAttributesFromEntry( $replacingObject );
		//replacement as a result of trimming
		if ( !is_null($clipAttributes) ) {
			kEventsManager::setForceDeferredEvents( true );
			$this->deleteCuePoints($c);
			//copy cuepoints from replacement entry
			$replacementCuePoints = CuePointPeer::retrieveByEntryId($replacingObject->getId());
			foreach( $replacementCuePoints as $cuePoint ) {
				$newCuePoint = $cuePoint->copyToEntry($object);
				$newCuePoint->save();
			}
			kEventsManager::flushEvents();
		} else if (PermissionPeer::isValidForPartner(CuePointPermissionName::REMOVE_CUE_POINTS_WHEN_REPLACING_MEDIA, $object->getPartnerId())) {
			$this->deleteCuePoints($c);
		}

		return true;
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
	 * @param CuePoint $cuePoint
	 */
	protected function cuePointAdded(CuePoint $cuePoint)
	{
		if($cuePoint->shouldReIndexEntry())
			$this->reIndexCuePointEntry($cuePoint);
	}
	
	/**
	 * @param CuePoint $cuePoint
	 */
	protected function cuePointDeleted(CuePoint $cuePoint) 
	{
		$c = new Criteria();
		$c->add(CuePointPeer::PARENT_ID, $cuePoint->getId());
			
		$this->deleteCuePoints($c);
			
		//re-index cue point on entry
		if($cuePoint->shouldReIndexEntry())
			$this->reIndexCuePointEntry($cuePoint);
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

		$update = new Criteria();
		$update->add(CuePointPeer::STATUS, CuePointStatus::DELETED);
			
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
		
		$entryId = $scene['entryId'];
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
			$this->reIndexCuePointEntry($object);
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
				&& !$object->hasMediaServer()
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
		return $object->shouldReIndexEntry($modifiedColumns);
	}

	public static function postProcessCuePoints( $liveEntry, $cuePointsIds = null )
	{
		$select = new Criteria();

		if ( $cuePointsIds )
		{
			$select->add(CuePointPeer::ID, $cuePointsIds, Criteria::IN);
		}
		else
		{
			/* @var $liveEntry LiveEntry */
			$select->add(CuePointPeer::ENTRY_ID, $liveEntry->getId());
			$select->add(CuePointPeer::STATUS, CuePointStatus::READY);

			$cuePoints = CuePointPeer::doSelect($select);
			$cuePointsIds = array();
			foreach($cuePoints as $cuePoint)
			{
				/* @var $cuePoint CuePoint */
				$cuePointsIds[] = $cuePoint->getId();
			}
		}

		$update = new Criteria();
		$update->add(CuePointPeer::STATUS, CuePointStatus::HANDLED);

		$con = Propel::getConnection(MetadataPeer::DATABASE_NAME);
		BasePeer::doUpdate($select, $update, $con);

		$cuePoints = CuePointPeer::retrieveByPKs($cuePointsIds);
		foreach($cuePoints as $cuePoint)
		{
			/* @var $cuePoint CuePoint */
			$cuePoint->indexToSearchIndex();
		}
	}

	/**
	 * @param entry $vodEntry
	 */
	public static function copyCuePointsFromLiveToVodEntry( $mediaInfo )
	{
		$vodEntry = self::getVodEntryBasedOnMediaInfoFlavorAsset( $mediaInfo );
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

		KalturaLog::log("Saving the live entry [{$liveEntry->getId()}] cue points into the associated VOD entry [{$vodEntry->getId()}]");

		// select up to MAX_CUE_POINTS_TO_COPY_TO_VOD to handle
		$c = new KalturaCriteria();
		$c->add( CuePointPeer::ENTRY_ID, $liveEntry->getId() );
		$c->add( CuePointPeer::START_TIME, $liveEntry->getLengthInMsecs(), KalturaCriteria::LESS_EQUAL ); // Don't copy future cuepoints
		$c->add( CuePointPeer::STATUS, CuePointStatus::READY ); // READY, but not yet HANDLED

		$c->addAscendingOrderByColumn(CuePointPeer::START_TIME);
		$c->setLimit( self::MAX_CUE_POINTS_TO_COPY_TO_VOD );
		$liveCuePointsToCopy = CuePointPeer::doSelect($c);

		$numLiveCuePointsToCopy = count($liveCuePointsToCopy);
		KalturaLog::info("About to copy $numLiveCuePointsToCopy cuepoints from live entry [{$liveEntry->getId()}] to VOD entry [{$vodEntry->getId()}]");

		$processedCuePointIds = array();

		if ( $numLiveCuePointsToCopy > 0 )
		{
			$recordedSegmentsInfo = $liveEntry->getRecordedSegmentsInfo();

			foreach ( $liveCuePointsToCopy as $liveCuePoint )
			{
				$processedCuePointIds[] = $liveCuePoint->getId();

				$cuePointCreationTime = $liveCuePoint->getCreatedAt(NULL)*1000;
				$offsetForTS = $recordedSegmentsInfo->getOffsetForTimestamp($cuePointCreationTime);

				$copyMsg = "cuepoint [{$liveCuePoint->getId()}] from live entry [{$liveEntry->getId()}] to VOD entry [{$vodEntry->getId()}]";
				KalturaLog::debug("Preparing to copy $copyMsg");

				if ( ! is_null( $offsetForTS ) )
				{
					$liveCuePoint->copyFromLiveToVodEntry( $vodEntry, $offsetForTS );
				}
				else
				{
					KalturaLog::info("Not copying $copyMsg" );
				}				
			}
		}

		KalturaLog::info("Post processing cuePointIds for live entry [{$liveEntry->getId()}]: " . print_r($processedCuePointIds,true) );
		if ( count($processedCuePointIds) )
		{
			self::postProcessCuePoints( $liveEntry, $processedCuePointIds );
		}
	}
	
	protected function reIndexCuePointEntry(CuePoint $cuePoint)
	{
		//index the entry after the cue point was added|deleted
		$entryId = $cuePoint->getEntryId();
		$entry = entryPeer::retrieveByPK($entryId);

		if($entry){
			$entry->setUpdatedAt(time());
			$entry->save();
			$entry->indexToSearchIndex();
		}
	}

	/**
	 *
	 * @param entry $clipEntry new entry to copy and adjust cue points from root entry to
	 */
	public static function copyCuePointsToClipEntry( entry $clipEntry ) {
		$clipAtts =  self::getClipAttributesFromEntry( $clipEntry );
		if ( !is_null($clipAtts) ) {
			$sourceEntry = entryPeer::retrieveByPK( $clipEntry->getSourceEntryId() );
			if ( is_null($sourceEntry) ) {
				KalturaLog::info("Didn't copy cuePoints for entry [{$clipEntry->getId()}] because source entry [" . $clipEntry->getSourceEntryId() . "] wasn't found");
				return;
			}
			$sourceEntryDuration = $sourceEntry->getLengthInMsecs();

			$clipStartTime = $clipAtts->getOffset();
			if ( is_null($clipStartTime) )
				$clipStartTime = 0;
			$clipDuration = $clipAtts->getDuration();
			if ( is_null($clipDuration) )
				$clipDuration = $sourceEntryDuration;


			$c = new KalturaCriteria();
			$c->add( CuePointPeer::ENTRY_ID, $clipEntry->getSourceEntryId() );
			if ( $clipDuration < $sourceEntryDuration ) {
				$c->addAnd( CuePointPeer::START_TIME, $clipStartTime + $clipDuration, KalturaCriteria::LESS_EQUAL );
			}
			if ( $clipStartTime > 0 ) {
				$c->addAnd( CuePointPeer::START_TIME, $clipStartTime, KalturaCriteria::GREATER_EQUAL );
				$c->addOr( CuePointPeer::START_TIME, 0, KalturaCriteria::EQUAL );
			}

			$c->addAscendingOrderByColumn(CuePointPeer::CREATED_AT);
			$rootEntryCuePointsToCopy = CuePointPeer::doSelect($c);

			if ( count( $rootEntryCuePointsToCopy ) <= self::MAX_CUE_POINTS_TO_COPY_TO_CLIP )
			{
				foreach( $rootEntryCuePointsToCopy as $cuePoint ) {
					$cuePoint->copyToClipEntry( $clipEntry, $clipStartTime, $clipDuration );
				}
			} else {
				KalturaLog::alert("Can't copy cuePoints for entry [{$clipEntry->getId()}] because cuePoints count exceeded max limit of [" . self::MAX_CUE_POINTS_TO_COPY_TO_CLIP . "]");
			}
		}
	}

}