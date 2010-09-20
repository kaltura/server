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
			KalturaLog::debug("Partner id is null");
			return false;
		}
			
		$partner = PartnerPeer::retrieveByPK($partnerId);
		if(!$partner)
		{
			KalturaLog::debug("Partner not found");
			return false;
		}
			
		if(!$partner->getPluginEnabled(AuditPlugin::PLUGIN_NAME))
		{
			KalturaLog::debug("Parner audit trail is disabled");
			return false;
		}
			
		// validate only partner
		if(is_null($auditTrail))
			return true;
			
		$auditTrailConfig = self::getAuditTrailConfig($partnerId, $auditTrail->getObjectType());
		if(is_null($auditTrailConfig))
		{
			KalturaLog::debug("Audit trail config not found");
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
			KalturaLog::debug("Audit trail config cache path [$cachePath]");
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
		return kCurrentContext::$partner_id;
	}

	/**
	 * @param BaseObject $object
	 * @return AuditTrail
	 */
	public function createAuditTrail(BaseObject $object) 
	{
		$partnerId = $this->getPartnerId($object);
		if(!$this->traceEnabled($partnerId))
			return null;
			
		if(!method_exists($object, 'getPeer') || !method_exists($object, 'getId'))
			return null;
			
		$peer = $object->getPeer();
		$objectType = $peer->getOMClass(false);
		
		KalturaLog::debug("Creating audit trail for object id[" . $object->getId() . "] type[$objectType]");
		
		try
		{
			$auditTrail = new AuditTrail();
			$auditTrail->setPartnerId($partnerId);
			$auditTrail->setObjectType($objectType);
			$auditTrail->setStatus(KalturaAuditTrailStatus::READY);
			$auditTrail->setObjectId($object->getId());
			$auditTrail->setEntryId($this->getEntryId($object));
		}
		catch(kAuditTrailException $e)
		{
			KalturaLog::err("Error creating audit trail for object id[" . $object->getId() . "] type[$objectType] " . $e->getMessage());
			$auditTrail = null;
		}
		
		return $auditTrail;
	}

	/**
	 * @param int $objectType
	 * @param int $objectSubType
	 * @return KalturaAuditTrailFileSyncSubType
	 */
	public static function translateSubTypeToEnum($objectType, $objectSubType) 
	{
		switch($objectType)
		{
			case FileSync::FILE_SYNC_OBJECT_TYPE_ENTRY:
				switch($objectSubType)
				{
					case entry::FILE_SYNC_ENTRY_SUB_TYPE_DATA:
						return KalturaAuditTrailFileSyncSubType::ENTRY_DATA;
					case entry::FILE_SYNC_ENTRY_SUB_TYPE_DATA_EDIT:
						return KalturaAuditTrailFileSyncSubType::ENTRY_DATA_EDIT;
					case entry::FILE_SYNC_ENTRY_SUB_TYPE_THUMB:
						return KalturaAuditTrailFileSyncSubType::ENTRY_THUMB;
					case entry::FILE_SYNC_ENTRY_SUB_TYPE_ARCHIVE:
						return KalturaAuditTrailFileSyncSubType::ENTRY_ARCHIVE;
					case entry::FILE_SYNC_ENTRY_SUB_TYPE_DOWNLOAD:
						return KalturaAuditTrailFileSyncSubType::ENTRY_DOWNLOAD;
					case entry::FILE_SYNC_ENTRY_SUB_TYPE_OFFLINE_THUMB:
						return KalturaAuditTrailFileSyncSubType::ENTRY_OFFLINE_THUMB;
					case entry::FILE_SYNC_ENTRY_SUB_TYPE_ISM:
						return KalturaAuditTrailFileSyncSubType::ENTRY_ISM;
					case entry::FILE_SYNC_ENTRY_SUB_TYPE_ISMC:
						return KalturaAuditTrailFileSyncSubType::ENTRY_ISMC;
					case entry::FILE_SYNC_ENTRY_SUB_TYPE_CONVERSION_LOG:
						return KalturaAuditTrailFileSyncSubType::ENTRY_CONVERSION_LOG;
				}
				return null;
				
			case FileSync::FILE_SYNC_OBJECT_TYPE_UICONF:
				switch($objectSubType)
				{
					case uiConf::FILE_SYNC_UICONF_SUB_TYPE_DATA:
						return KalturaAuditTrailFileSyncSubType::UICONF_DATA;
					case uiConf::FILE_SYNC_UICONF_SUB_TYPE_FEATURES:
						return KalturaAuditTrailFileSyncSubType::UICONF_FEATURES;
				}
				return null;
				
			case FileSync::FILE_SYNC_OBJECT_TYPE_BATCHJOB:
				switch($objectSubType)
				{
					case BatchJob::FILE_SYNC_BATCHJOB_SUB_TYPE_BULKUPLOADCSV:
						return KalturaAuditTrailFileSyncSubType::BATCHJOB_BULKUPLOADCSV;
					case BatchJob::FILE_SYNC_BATCHJOB_SUB_TYPE_BULKUPLOADLOG:
						return KalturaAuditTrailFileSyncSubType::BATCHJOB_BULKUPLOADLOG;
					case BatchJob::FILE_SYNC_BATCHJOB_SUB_TYPE_CONFIG:
						return KalturaAuditTrailFileSyncSubType::BATCHJOB_CONFIG;
				}
				return null;
				
			case FileSync::FILE_SYNC_OBJECT_TYPE_FLAVOR_ASSET:
				switch($objectSubType)
				{
					case flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET:
						return KalturaAuditTrailFileSyncSubType::FLAVOR_ASSET_ASSET;
					case flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_CONVERT_LOG:
						return KalturaAuditTrailFileSyncSubType::FLAVOR_ASSET_CONVERT_LOG;
				}
				return null;
				
			case FileSync::FILE_SYNC_OBJECT_TYPE_METADATA:
				if(!class_exists('Metadata'))
					return null;
					
				switch($objectSubType)
				{
					case Metadata::FILE_SYNC_METADATA_DATA:
						return KalturaAuditTrailFileSyncSubType::METADATA_DATA;
				}
				return null;
				
			case FileSync::FILE_SYNC_OBJECT_TYPE_METADATA_PROFILE:
				if(!class_exists('MetadataProfile'))
					return null;
					
				switch($objectSubType)
				{
					case MetadataProfile::FILE_SYNC_METADATA_DEFINITION:
						return KalturaAuditTrailFileSyncSubType::METADATA_PROFILE_DEFINITION;
					case MetadataProfile::FILE_SYNC_METADATA_VIEWS:
						return KalturaAuditTrailFileSyncSubType::METADATA_PROFILE_VIEWS;
				}
				return null;
		}
		return null;
	}

	/**
	 * @param BaseObject $object
	 */
	public function fileSyncCreated(FileSync $fileSync) 
	{
		KalturaLog::debug("id [" . $fileSync->getId() . "]");
		$object = kFileSyncUtils::retrieveObjectForFileSync($fileSync);
		if(!$object || !($object instanceof ISyncableFile))
		{
			KalturaLog::debug("Not instance of ISyncableFile");
			return;
		}
			
		$auditTrail = self::createAuditTrail($object);
		if(!$auditTrail)
		{
			KalturaLog::debug("No audit created");
			return;
		}
			
		$data = new KalturaAuditTrailFileSyncCreateInfo();
		$data->version = $fileSync->getVersion();
		$data->objectSubType = self::translateSubTypeToEnum($fileSync->getObjectType(), $fileSync->getObjectSubType());
		$data->dc = $fileSync->getDc();
		$data->original = $fileSync->getOriginal();
		$data->fileType = $fileSync->getFileType();
		
		$auditTrail->setData($data);
		$auditTrail->setAction(KalturaAuditTrailAction::FILE_SYNC_CREATED);
		$auditTrail->save();
	}

	/**
	 * @param BaseObject $object
	 */
	public function objectCreated(BaseObject $object) 
	{
		if($object instanceof FileSync)
			$this->fileSyncCreated($object);
			
		$auditTrail = self::createAuditTrail($object);
		if(!$auditTrail)
			return;
			
		$auditTrail->setAction(KalturaAuditTrailAction::CREATED);
		$auditTrail->save();
	}

	/**
	 * @param BaseObject $fromObject
	 * @param BaseObject $toObject
	 */
	public function objectCopied(BaseObject $fromObject, BaseObject $toObject) 
	{
		$auditTrail = self::createAuditTrail($toObject);
		if(!$auditTrail)
			return;
			
		$auditTrail->setAction(KalturaAuditTrailAction::COPIED);
		$auditTrail->save();
	}

	/**
	 * @param BaseObject $object
	 */
	public function objectDeleted(BaseObject $object) 
	{
		$auditTrail = self::createAuditTrail($object);
		if(!$auditTrail)
			return;
			
		$auditTrail->setAction(KalturaAuditTrailAction::DELETED);
		$auditTrail->save();
	}

	/**
	 * @param BaseObject $object
	 * @param array $modifiedColumns
	 */
	public function objectChanged(BaseObject $object, array $modifiedColumns) 
	{
		$auditTrail = self::createAuditTrail($object);
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
		KalturaLog::debug("Audit trail supported descriptors: " . print_r($supportedDescriptors, true));
		
		$changedItems = new KalturaAuditTrailChangeItemArray();
		foreach($columnsOldValues as $column => $oldValue)
		{
			if(!in_array($column, $supportedDescriptors))
			{
				KalturaLog::debug("Audit trail for object type[" . $auditTrail->getObjectType() . "] column[$column] not supported");
				continue;
			}
				
			$changedItem = new KalturaAuditTrailChangeItem();
			$changedItem->descriptor = $column;
			$changedItem->oldValue = $oldValue;
			$changedItem->newValue = $object->getByName($column, BasePeer::TYPE_COLNAME);
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
					KalturaLog::debug("Audit trail for object type[" . $auditTrail->getObjectType() . "] descriptor[$descriptor] not supported");
					continue;
				}
					
				$changedItem = new KalturaAuditTrailChangeItem();
				$changedItem->descriptor = $descriptor;
				$changedItem->oldValue = $oldValue;
				$changedItem->newValue = $object->getFromCustomData($name, $namespace);
				$changedItems[] = $changedItem;
			}
		}
		if(!$changedItems->count)
			return;
			
		$data = new KalturaAuditTrailChangeInfo();
		$data->changedItems = $changedItems;
		
		$auditTrail->setData($data);
		$auditTrail->setAction(KalturaAuditTrailAction::CHANGED);
		$auditTrail->save();
	}

}