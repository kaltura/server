<?php
/**
 * @package plugins.cuePoint
 */
class kCuePointManager implements kObjectDeletedEventConsumer, kObjectChangedEventConsumer, kObjectAddedEventConsumer
{
	const MAX_CUE_POINTS_TO_COPY_TO_VOD = 100;

	/* (non-PHPdoc)
	 * @see kObjectAddedEventConsumer::shouldConsumeAddedEvent()
	 */
	public function shouldConsumeAddedEvent(BaseObject $object)
	{
		if($object instanceof CuePoint)
			return true;
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
	
	/**
	 * @param CuePoint $cuePoint
	 */
	protected function cuePointAdded(CuePoint $cuePoint)
	{
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
			kEventsManager::raiseEvent(new kObjectDeletedEvent($cuePoint));
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
			
		KalturaLog::debug('xml [' . file_get_contents($xmlPath) . ']');
		$xml = new KDOMDocument();
		libxml_use_internal_errors(true);
		libxml_clear_errors();
		if(!$xml->load($xmlPath))
		{
			$errorMessage = kXml::getLibXmlErrorDescription(file_get_contents($xmlPath));
			throw new kCuePointException("XML [$xmlPath] is invalid:\n{$errorMessage}", kCuePointException::XML_INVALID);
		}
		
		$xsdPath = SchemaService::getSchemaPath(CuePointPlugin::getApiValue(CuePointSchemaType::INGEST_API));
		KalturaLog::debug("xsd path [$xsdPath]");
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
				KalturaLog::debug("cue point [" . get_class($cuePoint) . "] created [" . $cuePoint->getId() . "]");
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
	
		KalturaLog::debug("xml [$xmlContent]");
		$xml = new KDOMDocument();
		libxml_use_internal_errors(true);
		libxml_clear_errors();
		if(!$xml->loadXML($xmlContent))
		{
			$errorMessage = kXml::getLibXmlErrorDescription($xmlContent);
			throw new kCuePointException("XML is invalid:\n{$errorMessage}", kCuePointException::XML_INVALID);
		}
		
		$xsdPath = SchemaService::getSchemaPath($schemaType);
		KalturaLog::debug("xsd path [$xsdPath]");
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
		if ( self::isCopyCuePointsFromLiveToVodEvent($object, $modifiedColumns) )
		{
			self::copyCuePointsFromLiveToVodEntry( $object );
		}

		if ( self::isPostProcessCuePointsEvent( $object, $modifiedColumns ) )
		{
			self::postProcessCuePoints( $object );
		}
		
		if(self::shouldReIndexEntry($object, $modifiedColumns))
		{
			$this->reIndexCuePointEntry($object);
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

		if ( self::isCopyCuePointsFromLiveToVodEvent($object, $modifiedColumns) )
		{
			return true;
		}
		
		if( self::shouldReIndexEntry($object, $modifiedColumns) )
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
	
	public static function isCopyCuePointsFromLiveToVodEvent(BaseObject $object, array $modifiedColumns)
	{
		if ( $object instanceof entry
				&& $object->getSourceType() == EntrySourceType::RECORDED_LIVE
				&& $object->getRootEntryId()
				&& in_array(entryPeer::LENGTH_IN_MSECS, $modifiedColumns)
			)
		{
			return true;
		}

		return false;
	}
	
	public static function shouldReIndexEntry(BaseObject $object, array $modifiedColumns)
	{
		if(!($object instanceof CuePoint))
			return false;
		
		$indexOnEntryTypes = CuePointPlugin::getIndexOnEntryTypes();
		if(!count($indexOnEntryTypes))
			return false;
			
		if(!in_array($object->getType(), $indexOnEntryTypes))
			return false;
		
		$fieldsToMonitor = array(CuePointPeer::TEXT, CuePointPeer::TAGS, CuePointPeer::NAME);
		
		if(count(array_intersect($fieldsToMonitor, $modifiedColumns)) > 0)
			return true;

		return false;
	}

	public static function postProcessCuePoints( $liveEntry, $maxStartTime = null )
	{
		/* @var $object LiveEntry */
		$select = new Criteria();
		$select->add(CuePointPeer::ENTRY_ID, $liveEntry->getId());
		$select->add(CuePointPeer::STATUS, CuePointStatus::READY);

		if ( ! is_null( $maxStartTime ) )
		{
			KalturaLog::debug("Limiting cupied cuepoints to maxStartTime $maxStartTime");
			$select->add( CuePointPeer::START_TIME, $maxStartTime, KalturaCriteria::LESS_EQUAL );
		}

		$cuePoints = CuePointPeer::doSelect($select);
		$cuePointsIds = array();
		foreach($cuePoints as $cuePoint)
		{
			/* @var $cuePoint CuePoint */
			$cuePointsIds[] = $cuePoint->getId();
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
	 * @param LiveEntry $liveEntry
	 */
	public static function copyCuePointsFromLiveToVodEntry( $vodEntry )
	{
		$liveEntryId = $vodEntry->getRootEntryId();
		/** @var $liveEntry KalturaLiveEntry */
		$liveEntry = entryPeer::retrieveByPK( $liveEntryId );
		if ( ! $liveEntry || ! $liveEntry instanceof LiveEntry )
		{
			KalturaLog::debug("Can't find live entry with id [$liveEntryId]");
			return;
		}

		KalturaLog::log("Saving the live entry [{$liveEntry->getId()}] cue points into the associated VOD entry [{$vodEntry->getId()}]");

		$maxStartTime = $liveEntry->getLengthInMsecs();

		$c = new KalturaCriteria();
		$c->add( CuePointPeer::ENTRY_ID, $liveEntry->getId() );
		$c->add( CuePointPeer::START_TIME, $maxStartTime, KalturaCriteria::LESS_EQUAL ); // Don't copy future cuepoints
		$c->add( CuePointPeer::STATUS, CuePointStatus::READY ); // READY, but not yet HANDLED

		$c->addAscendingOrderByColumn(CuePointPeer::START_TIME);
		$c->setLimit( self::MAX_CUE_POINTS_TO_COPY_TO_VOD );
		$liveCuePointsToCopy = CuePointPeer::doSelect($c);

		$numLiveCuePointsToCopy = count($liveCuePointsToCopy);
		KalturaLog::debug("About to copy $numLiveCuePointsToCopy cuepoints from live entry [{$liveEntry->getId()}] to VOD entry [{$vodEntry->getId()}]");

		if ( $numLiveCuePointsToCopy > 0 )
		{
			$recordedSegmentsInfo = $liveEntry->getRecordedSegmentsInfo();
			foreach ( $liveCuePointsToCopy as $liveCuePoint )
			{
				$startTime = $liveCuePoint->getStartTime();

				$copyMsg = "cuepoint [{$liveCuePoint->getId()}] from live entry [{$liveEntry->getId()}] to VOD entry [{$vodEntry->getId()}] with startTime [$startTime]";
				KalturaLog::debug("Preparing to copy $copyMsg");

				$totalVodOffsetTime = $recordedSegmentsInfo->getTotalVodTimeOffset( $startTime );

				if ( ! is_null( $totalVodOffsetTime ) )
				{
					$adjustedStartTime = $startTime - $totalVodOffsetTime;
					KalturaLog::debug("Copying $copyMsg and adjustedStartTime [$adjustedStartTime] (totalVodOffsetTime [$totalVodOffsetTime])" );
					$liveCuePoint->copyFromLiveToVodEntry( $liveEntry, $vodEntry, $adjustedStartTime );
				}
				else
				{
					KalturaLog::debug("Not copying $copyMsg" );
				}				
			}
		}

		$liveEntry->save();

		self::postProcessCuePoints( $liveEntry, $maxStartTime );
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
}