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
		$data->objectSubType = $fileSync->getObjectSubType();
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