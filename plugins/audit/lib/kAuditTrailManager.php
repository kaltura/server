<?php
class kAuditTrailManager implements kObjectChangedEventConsumer, kObjectCopiedEventConsumer, kObjectCreatedEventConsumer, kObjectDeletedEventConsumer
{
	protected static $cachedPartnerConfig = array();
	
	/**
	 * @param BaseObject $object
	 * @return bool
	 */
	public static function traceEnabled($partnerId, AuditTrail $auditTrail = null) 
	{
		if(is_null($partnerId))
		{
//			KalturaLog::debug("Partner id is null");
			return false;
		}
			
		$partner = PartnerPeer::retrieveByPK($partnerId);
		if(!$partner)
		{
//			KalturaLog::debug("Partner not found");
			return false;
		}
			
		if(!$partner->getPluginEnabled(AuditPlugin::PLUGIN_NAME))
		{
//			KalturaLog::debug("Partner audit trail is disabled");
			return false;
		}
			
		// validate only partner
		if(is_null($auditTrail))
			return true;
			
		$auditTrailConfig = self::getAuditTrailConfig($partnerId, $auditTrail->getObjectType());
		if(is_null($auditTrailConfig))
		{
//			KalturaLog::debug("Audit trail config not found");
			return false;
		}
			
		return $auditTrailConfig->actionEnabled($auditTrail->getAction());
	}
			
	/**
	 * @param int $partnerId
	 * @param string $objectType
	 * @return AuditTrailConfig
	 */
	protected static function getAuditTrailConfig($partnerId, $objectType) 
	{
		$config = null;
		if(isset(self::$cachedPartnerConfig[$partnerId]))
		{
			$config = self::$cachedPartnerConfig[$partnerId];
		}
		else
		{
			$cachePath = realpath( dirname(__FILE__) . '/../cache') . "/$partnerId.cfg";
//			KalturaLog::debug("Audit trail config cache path [$cachePath]");
			if(file_exists($cachePath))
			{
				$config = unserialize(file_get_contents($cachePath));
			}
			else
			{
				$auditTrailConfigs = AuditTrailConfigPeer::retrieveByPartnerId($partnerId);
				$config = array();
				if(count($auditTrailConfigs))
				{
					foreach($auditTrailConfigs as $auditTrailConfig)
						$config[$auditTrailConfig->getObjectType()] = $auditTrailConfig;
				}
				
				file_put_contents($cachePath, serialize($config));
			}
			
			self::$cachedPartnerConfig[$partnerId] = $config;
		}
		
		if(!isset($config[$objectType]))
			return null;
			
		return $config[$objectType];
	}

	/**
	 * @param BaseObject $object
	 */
	protected function setRelatedObject(AuditTrail $auditTrail, BaseObject $object) 
	{
		if(class_exists('Metadata') && $object instanceof Metadata)
		{
			$auditTrail->setRelatedObjectType(AuditTrail::AUDIT_TRAIL_OBJECT_TYPE_METADATA_PROFILE);
			$auditTrail->setRelatedObjectId($object->getMetadataProfileId());
		}
		
		if($auditTrail->getAction() == AuditTrail::AUDIT_TRAIL_ACTION_FILE_SYNC_CREATED)
		{
			$peer = $object->getPeer();
			$objectType = $peer->getOMClass(false, null);
			
			$auditTrail->setRelatedObjectType($objectType);
			$auditTrail->setRelatedObjectId($object->getId());
		}
		
		if($object instanceof FileSync)
		{
			switch($object->getObjectType())
			{
				case FileSync::FILE_SYNC_OBJECT_TYPE_ENTRY:
					$auditTrail->setRelatedObjectType(AuditTrail::AUDIT_TRAIL_OBJECT_TYPE_ENTRY);
					$auditTrail->setRelatedObjectId($object->getObjectId());
					$auditTrail->setEntryId($object->getObjectId());
					break;
					
				case FileSync::FILE_SYNC_OBJECT_TYPE_UICONF:
					$auditTrail->setRelatedObjectType(AuditTrail::AUDIT_TRAIL_OBJECT_TYPE_UI_CONF);
					$auditTrail->setRelatedObjectId($object->getObjectId());
					break;
					
				case FileSync::FILE_SYNC_OBJECT_TYPE_BATCHJOB:
					$auditTrail->setRelatedObjectType(AuditTrail::AUDIT_TRAIL_OBJECT_TYPE_BATCH_JOB);
					$auditTrail->setRelatedObjectId($object->getObjectId());
					
					$batchJob = BatchJobPeer::retrieveByPK($object->getObjectId());
					if($batchJob)
						$auditTrail->setEntryId($batchJob->getEntryId());
						
					break;
					
				case FileSync::FILE_SYNC_OBJECT_TYPE_FLAVOR_ASSET:
					$auditTrail->setRelatedObjectType(AuditTrail::AUDIT_TRAIL_OBJECT_TYPE_FLAVOR_ASSET);
					$auditTrail->setRelatedObjectId($object->getObjectId());
					
					$flavorAsset = flavorAssetPeer::retrieveById($object->getObjectId());
					if($flavorAsset)
						$auditTrail->setEntryId($flavorAsset->getEntryId());
						
					break;
					
				case FileSync::FILE_SYNC_OBJECT_TYPE_METADATA:
					$auditTrail->setRelatedObjectType(AuditTrail::AUDIT_TRAIL_OBJECT_TYPE_METADATA);
					$auditTrail->setRelatedObjectId($object->getObjectId());
					
					if(class_exists('Metadata'))
					{
						$metadata = MetadataPeer::retrieveByPK($object->getObjectId());
						if($metadata && $metadata->getObjectType() == Metadata::TYPE_ENTRY)
							$auditTrail->setEntryId($metadata->getObjectId());
					}
					break;
					
				case FileSync::FILE_SYNC_OBJECT_TYPE_METADATA_PROFILE:
					$auditTrail->setRelatedObjectType(AuditTrail::AUDIT_TRAIL_OBJECT_TYPE_METADATA_PROFILE);
					$auditTrail->setRelatedObjectId($object->getObjectId());
					break;
			}
		}
	}

	/**
	 * @param BaseObject $object
	 * @return string entry id
	 */
	protected function getEntryId(BaseObject $object) 
	{
		if($object instanceof entry)
			return $object->getId();
			
		if(method_exists($object, 'getEntryId'))
			return $object->getEntryId();
			
		if(class_exists('Metadata') && $object instanceof Metadata && $object->getObjectType() == Metadata::TYPE_ENTRY)
			return $object->getObjectId();
			
//		KalturaLog::info("Can't get entry id for object type [" . get_class($object) . "]");
		return null;
	}

	/**
	 * @param BaseObject $object
	 * @return string partner id
	 */
	protected function getPartnerId(BaseObject $object) 
	{
		if($object instanceof Partner)
			return $object->getId();
			
		if(method_exists($object, 'getPartnerId'))
			return $object->getPartnerId();
			
//		KalturaLog::info("Can't get partner id for object type [" . get_class($object) . "]");
		return kCurrentContext::$partner_id;
	}

	/**
	 * @param BaseObject $object
	 * @return AuditTrail
	 */
	public function createAuditTrail(BaseObject $object, $action) 
	{
		$partnerId = $this->getPartnerId($object);
		if(!$this->traceEnabled($partnerId))
			return null;
			
		if(!method_exists($object, 'getPeer') || !method_exists($object, 'getId'))
			return null;
			
		$peer = $object->getPeer();
		$objectType = $peer->getOMClass(false, null);
		
//		KalturaLog::debug("Creating audit trail for object id[" . $object->getId() . "] type[$objectType]");
		
		try
		{
			$auditTrail = new AuditTrail();
			$auditTrail->setAction($action);
			$auditTrail->setPartnerId($partnerId);
			$auditTrail->setObjectType($objectType);
			$auditTrail->setStatus(AuditTrail::AUDIT_TRAIL_STATUS_READY);
			$auditTrail->setObjectId($object->getId());
			$auditTrail->setEntryId($this->getEntryId($object));
			
			self::setRelatedObject($auditTrail, $object);
		}
		catch(kAuditTrailException $e)
		{
//			KalturaLog::err("Error creating audit trail for object id[" . $object->getId() . "] type[$objectType] " . $e->getMessage());
			$auditTrail = null;
		}
		
		return $auditTrail;
	}

	/**
	 * @param BaseObject $object
	 */
	public function fileSyncCreated(FileSync $fileSync) 
	{
//		KalturaLog::debug("id [" . $fileSync->getId() . "]");
		$object = kFileSyncUtils::retrieveObjectForFileSync($fileSync);
		if(!$object || !($object instanceof ISyncableFile))
		{
//			KalturaLog::debug("Not instance of ISyncableFile");
			return;
		}
			
		$auditTrail = self::createAuditTrail($object, AuditTrail::AUDIT_TRAIL_ACTION_FILE_SYNC_CREATED);
		if(!$auditTrail)
		{
//			KalturaLog::debug("No audit created");
			return;
		}
			
		$data = new kAuditTrailFileSyncCreateInfo();
		$data->setVersion($fileSync->getVersion());
		$data->setObjectSubType($fileSync->getObjectSubType());
		$data->setDc($fileSync->getDc());
		$data->setOriginal($fileSync->getOriginal());
		$data->setFileType($fileSync->getFileType());
		
		$auditTrail->setData($data);
		$auditTrail->save();
	}

	/**
	 * @param BaseObject $object
	 */
	public function objectCreated(BaseObject $object) 
	{
		if($object instanceof FileSync)
			$this->fileSyncCreated($object);
			
		$auditTrail = self::createAuditTrail($object, AuditTrail::AUDIT_TRAIL_ACTION_CREATED);
		if(!$auditTrail)
			return;
			
		$auditTrail->save();
	}

	/**
	 * @param BaseObject $fromObject
	 * @param BaseObject $toObject
	 */
	public function objectCopied(BaseObject $fromObject, BaseObject $toObject) 
	{
		$auditTrail = self::createAuditTrail($toObject, AuditTrail::AUDIT_TRAIL_ACTION_COPIED);
		if(!$auditTrail)
			return;
			
		$auditTrail->save();
	}

	/**
	 * @param BaseObject $object
	 */
	public function objectDeleted(BaseObject $object) 
	{
		$auditTrail = self::createAuditTrail($object, AuditTrail::AUDIT_TRAIL_ACTION_DELETED);
		if(!$auditTrail)
			return;
			
		$auditTrail->save();
	}

	/**
	 * @param BaseObject $object
	 * @param array $modifiedColumns
	 */
	public function objectChanged(BaseObject $object, array $modifiedColumns) 
	{
		$auditTrail = self::createAuditTrail($object, AuditTrail::AUDIT_TRAIL_ACTION_CHANGED);
		if(!$auditTrail)
			return;
			
		if(!method_exists($object, 'getColumnsOldValues') || !method_exists($object, 'getByName'))
			return;
			
		$columnsOldValues = $object->getColumnsOldValues();
		$customDataOldValues = array();
			
		if(method_exists($object, 'getCustomDataOldValues'))
			$customDataOldValues = $object->getCustomDataOldValues();
		
		$auditTrailConfig = self::getAuditTrailConfig($auditTrail->getPartnerId(), $auditTrail->getObjectType());
		if(!$auditTrailConfig)
			return;
			
		$supportedDescriptors = explode(',', $auditTrailConfig->getDescriptors());
//		KalturaLog::debug("Audit trail supported descriptors: " . print_r($supportedDescriptors, true));
		
		$changedItems = array();
		foreach($columnsOldValues as $column => $oldValue)
		{
			if(!in_array($column, $supportedDescriptors))
			{
//				KalturaLog::debug("Audit trail for object type[" . $auditTrail->getObjectType() . "] column[$column] not supported");
				continue;
			}

			$newValue = $object->getByName($column, BasePeer::TYPE_COLNAME);
			if($newValue == $oldValue)
			{
//				KalturaLog::debug("Old and new values are identical [$column]");
				continue;
			}
			
			$changedItem = new kAuditTrailChangeItem();
			$changedItem->setDescriptor($column);
			$changedItem->setOldValue($oldValue);
			$changedItem->setNewValue($newValue);
			$changedItems[] = $changedItem;
		}
		foreach($customDataOldValues as $namespace => $oldValues)
		{
			if(!is_array($oldValues))
				continue;
			
			if(!strlen($namespace))
				$namespace = null;
		
			$prefix = is_null($namespace) ? '' : "$namespace/";
					
			foreach($oldValues as $name => $oldValue)
			{
				$descriptor = $prefix . $name;
				if(!in_array($descriptor, $supportedDescriptors))
				{
//					KalturaLog::debug("Audit trail for object type[" . $auditTrail->getObjectType() . "] descriptor[$descriptor] not supported");
					continue;
				}
			
				$newValue = $object->getFromCustomData($name, $namespace);
				if($newValue == $oldValue)
				{
//					KalturaLog::debug("Old and new values are identical [$descriptor]");
					continue;
				}
				
				$changedItem = new kAuditTrailChangeItem();
				$changedItem->setDescriptor($descriptor);
				$changedItem->setOldValue($oldValue);
				$changedItem->setNewValue($newValue);
				$changedItems[] = $changedItem;
			}
		}
		if(!count($changedItems))
			return;
			
		$data = new kAuditTrailChangeInfo();
		$data->setChangedItems($changedItems);
		
		$auditTrail->setData($data);
		$auditTrail->save();
	}

}