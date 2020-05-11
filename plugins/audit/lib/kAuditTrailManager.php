<?php
class kAuditTrailManager implements kObjectChangedEventConsumer, kObjectCopiedEventConsumer, kObjectCreatedEventConsumer, kObjectDeletedEventConsumer
{
	protected static $cachedPartnerEnabled = array();
	protected static $cachedPartnerConfig = array();
	
	/**
	 * @param BaseObject $object
	 * @return bool
	 */
	public static function traceEnabled($partnerId, AuditTrail $auditTrail = null) 
	{
		if(is_null($partnerId))
		{
			KalturaLog::info("Partner id is null");
			return false;
		}
		
		if(!isset(self::$cachedPartnerEnabled[$partnerId]))
		{
			$partner = PartnerPeer::retrieveByPK($partnerId);
			if(!$partner)
			{
				KalturaLog::info("Partner not found");
				return false;
			}
				
			if(!$partner->getPluginEnabled(AuditPlugin::PLUGIN_NAME))
			{
				KalturaLog::info("Partner audit trail is disabled");
				self::$cachedPartnerEnabled[$partnerId] = false;
			}
			else
			{
				self::$cachedPartnerEnabled[$partnerId] = true;
			}
		}
			
		// validate only partner
		if(is_null($auditTrail))
			return self::$cachedPartnerEnabled[$partnerId];
			
		$auditTrailConfig = self::getAuditTrailConfig($partnerId, $auditTrail->getObjectType());
		if(is_null($auditTrailConfig))
		{
			KalturaLog::info("Audit trail config not found");
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
			$cacheFolder = kConf::get("cache_root_path") . "/audit";
			if(!is_dir($cacheFolder))
				mkdir($cacheFolder, 0777);
				
			$cachePath = "$cacheFolder/$partnerId.cfg";
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
		{
			KalturaLog::info("Object type [$objectType] not audited");
			return null;
		}
			
		return $config[$objectType];
	}

	/**
	 * @param BaseObject $object
	 */
	protected function setRelatedObject(AuditTrail $auditTrail, BaseObject $object) 
	{
		if(class_exists('Metadata') && $object instanceof Metadata)
		{
			$auditTrail->setRelatedObjectType(AuditTrailObjectType::METADATA_PROFILE);
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
				case FileSyncObjectType::ENTRY:
					$auditTrail->setRelatedObjectType(AuditTrailObjectType::ENTRY);
					$auditTrail->setRelatedObjectId($object->getObjectId());
					$auditTrail->setEntryId($object->getObjectId());
					break;
					
				case FileSyncObjectType::UICONF:
					$auditTrail->setRelatedObjectType(AuditTrailObjectType::UI_CONF);
					$auditTrail->setRelatedObjectId($object->getObjectId());
					break;
					
				case FileSyncObjectType::BATCHJOB:
					$auditTrail->setRelatedObjectType(AuditTrailObjectType::BATCH_JOB);
					$auditTrail->setRelatedObjectId($object->getObjectId());
					
					$batchJob = BatchJobPeer::retrieveByPK($object->getObjectId());
					if($batchJob)
						$auditTrail->setEntryId($batchJob->getEntryId());
						
					break;
					
				case FileSyncObjectType::FLAVOR_ASSET:
					$auditTrail->setRelatedObjectType(AuditTrailObjectType::FLAVOR_ASSET);
					$auditTrail->setRelatedObjectId($object->getObjectId());
					
					$flavorAsset = assetPeer::retrieveById($object->getObjectId());
					if($flavorAsset)
						$auditTrail->setEntryId($flavorAsset->getEntryId());
						
					break;
					
				case FileSyncObjectType::METADATA:
					$auditTrail->setRelatedObjectType(AuditTrailObjectType::METADATA);
					$auditTrail->setRelatedObjectId($object->getObjectId());
					
					if(class_exists('Metadata'))
					{
						$metadata = MetadataPeer::retrieveByPK($object->getObjectId());
						if($metadata && $metadata->getObjectType() == MetadataObjectType::ENTRY)
							$auditTrail->setEntryId($metadata->getObjectId());
					}
					break;
					
				case FileSyncObjectType::METADATA_PROFILE:
					$auditTrail->setRelatedObjectType(AuditTrailObjectType::METADATA_PROFILE);
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
			
		if(class_exists('Metadata') && $object instanceof Metadata && $object->getObjectType() == MetadataObjectType::ENTRY)
			return $object->getObjectId();
			
		KalturaLog::info("Can't get entry id for object type [" . get_class($object) . "]");
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
			
		KalturaLog::info("Can't get partner id for object type [" . get_class($object) . "]");
		
		if(kCurrentContext::$partner_id)
			return kCurrentContext::$partner_id;
		if(kCurrentContext::$ks_partner_id)
			return kCurrentContext::$ks_partner_id;
			
		return null;
	}

	/**
	 * @param BaseObject $object
	 * @return AuditTrail
	 */
	public function createAuditTrail(BaseObject $object, $action) 
	{
		$partnerId = kCurrentContext::$master_partner_id;
				
		if(!$this->traceEnabled($partnerId))
			return null;
			
		if(!method_exists($object, 'getPeer') || !method_exists($object, 'getId'))
			return null;
			
		$peer = $object->getPeer();
		try
		{
			$objectType = $peer->getOMClass(false, null);
		}
		catch(Exception $e)
		{
			KalturaLog::err("Error creating audit trail for object id[" . $object->getId() . "] type[$objectType] " . $e->getMessage());
			$auditTrail = null;
			return null;
		}
		
		if(!in_array($objectType, AuditTrail::getAllwodObjectTypes()))
			return null;
		
		try
		{
			$auditTrail = new AuditTrail();
			$auditTrail->setAction($action);
			$auditTrail->setPartnerId($partnerId);
			$auditTrail->setObjectType($objectType);
			$auditTrail->setStatus(AuditTrail::AUDIT_TRAIL_STATUS_READY);
			$auditTrail->setObjectId($object->getId());
			if ($objectType != KalturaAuditTrailObjectType::REACH_PROFILE)
			{
				$auditTrail->setEntryId($this->getEntryId($object));
			}
			
			self::setRelatedObject($auditTrail, $object);
		}
		catch(kAuditTrailException $e)
		{
			KalturaLog::err("Error creating audit trail for object id[" . $object->getId() . "] type[$objectType] " . $e->getMessage());
			$auditTrail = null;
		}
		
		return $auditTrail;
	}

	/**
	 * @param BaseObject $object
	 */
	public function fileSyncCreated(FileSync $fileSync) 
	{
		$object = kFileSyncUtils::retrieveObjectForFileSync($fileSync);
		if(!$object || !($object instanceof ISyncableFile))
		{
			KalturaLog::info("Not instance of ISyncableFile");
			return;
		}
			
		$auditTrail = self::createAuditTrail($object, AuditTrail::AUDIT_TRAIL_ACTION_FILE_SYNC_CREATED);
		if(!$auditTrail)
		{
			KalturaLog::info("No audit created");
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
	
	/* (non-PHPdoc)
	 * @see kObjectCreatedEventConsumer::shouldConsumeCreatedEvent()
	 */
	public function shouldConsumeCreatedEvent(BaseObject $object)
	{
		$partnerId = kCurrentContext::$master_partner_id;
		if(($partnerId == Partner::ADMIN_CONSOLE_PARTNER_ID || $partnerId > 0) && $this->traceEnabled($partnerId))
			return true;
		
		return false;
	}

	/* (non-PHPdoc)
	 * @see kObjectCreatedEventConsumer::objectCreated()
	 */
	public function objectCreated(BaseObject $object) 
	{
		if($object instanceof FileSync)
			$this->fileSyncCreated($object);
			
		$auditTrail = self::createAuditTrail($object, AuditTrail::AUDIT_TRAIL_ACTION_CREATED);
		if(!$auditTrail)
			return true;
			
		$auditTrail->save();
		return true;
	}
	
	/* (non-PHPdoc)
	 * @see kObjectCopiedEventConsumer::shouldConsumeCopiedEvent()
	 */
	public function shouldConsumeCopiedEvent(BaseObject $fromObject, BaseObject $toObject)
	{
		$partnerId = kCurrentContext::$master_partner_id;
		if(($partnerId == Partner::ADMIN_CONSOLE_PARTNER_ID || $partnerId > 0) && $this->traceEnabled($partnerId))
			return true;
			
		return false;
	}

	/* (non-PHPdoc)
	 * @see kObjectCopiedEventConsumer::objectCopied()
	 */
	public function objectCopied(BaseObject $fromObject, BaseObject $toObject) 
	{
		$auditTrail = self::createAuditTrail($toObject, AuditTrail::AUDIT_TRAIL_ACTION_COPIED);
		if(!$auditTrail)
			return true;
			
		$auditTrail->save();
		return true;
	}
	
	/* (non-PHPdoc)
	 * @see kObjectDeletedEventConsumer::shouldConsumeDeletedEvent()
	 */
	public function shouldConsumeDeletedEvent(BaseObject $object)
	{
		
		$partnerId = kCurrentContext::$master_partner_id;
		if(($partnerId == Partner::ADMIN_CONSOLE_PARTNER_ID || $partnerId > 0) && $this->traceEnabled($partnerId))
			return true;
			
		return false;
	}

	/* (non-PHPdoc)
	 * @see kObjectDeletedEventConsumer::objectDeleted()
	 */
	public function objectDeleted(BaseObject $object, BatchJob $raisedJob = null) 
	{
		$auditTrail = self::createAuditTrail($object, AuditTrail::AUDIT_TRAIL_ACTION_DELETED);
		if(!$auditTrail)
			return true;
			
		$auditTrail->save();
		return true;
	}
	
	/* (non-PHPdoc)
	 * @see kObjectChangedEventConsumer::shouldConsumeChangedEvent()
	 */
	public function shouldConsumeChangedEvent(BaseObject $object, array $modifiedColumns)
	{
		$partnerId = kCurrentContext::$master_partner_id;
		if(($partnerId == Partner::ADMIN_CONSOLE_PARTNER_ID || $partnerId > 0) && $this->traceEnabled($partnerId))
			return true;
			
		return false;		
	}

	/* (non-PHPdoc)
	 * @see kObjectChangedEventConsumer::objectChanged()
	 */
	public function objectChanged(BaseObject $object, array $modifiedColumns) 
	{
		$auditTrail = self::createAuditTrail($object, AuditTrail::AUDIT_TRAIL_ACTION_CHANGED);
		if(!$auditTrail)
			return true;
			
		if(!method_exists($object, 'getColumnsOldValues') || !method_exists($object, 'getByName'))
			return true;
			
		$columnsOldValues = $object->getColumnsOldValues();
		$customDataOldValues = array();
			
		if(method_exists($object, 'getCustomDataOldValues'))
			$customDataOldValues = $object->getCustomDataOldValues();
		
		$auditTrailConfig = self::getAuditTrailConfig($auditTrail->getPartnerId(), $auditTrail->getObjectType());
		if(!$auditTrailConfig)
			return true;
			
		$supportedDescriptors = explode(',', $auditTrailConfig->getDescriptors());
		KalturaLog::info("Audit trail supported descriptors: " . print_r($supportedDescriptors, true));
		
		$changedItems = array();
		foreach($columnsOldValues as $column => $oldValue)
		{
			if(!in_array($column, $supportedDescriptors))
			{
				KalturaLog::info("Audit trail for object type[" . $auditTrail->getObjectType() . "] column[$column] not supported");
				continue;
			}

			$newValue = $object->getByName($column, BasePeer::TYPE_COLNAME);
			if($newValue == $oldValue)
			{
				KalturaLog::info("Old and new values are identical [$column]");
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
					KalturaLog::info("Audit trail for object type[" . $auditTrail->getObjectType() . "] descriptor[$descriptor] not supported");
					continue;
				}
			
				$newValue = $object->getFromCustomData($name, $namespace);
				if($newValue == $oldValue)
				{
					KalturaLog::info("Old and new values are identical [$descriptor]");
					continue;
				}
				if ($auditTrail->getObjectType() == KalturaAuditTrailObjectType::REACH_PROFILE)
				{
					$oldValue = preg_replace('/[[:cntrl:]]/', '', $oldValue);
					$newValue = preg_replace('/[[:cntrl:]]/', '', $newValue);
				}

				$changedItem = new kAuditTrailChangeItem();
				$changedItem->setDescriptor($descriptor);
				$changedItem->setOldValue($oldValue);
				$changedItem->setNewValue($newValue);
				$changedItems[] = $changedItem;
			}
		}
		if(!count($changedItems))
			return true;
			
		$data = new kAuditTrailChangeInfo();
		$data->setChangedItems($changedItems);
		
		$auditTrail->setData($data);
		$auditTrail->save();
		
		return true;
	}

}