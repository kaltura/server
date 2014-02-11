<?php

/**
 * Subclass for representing a row from the 'storage_profile' table.
 *
 * 
 *
 * @package Core
 * @subpackage model
 */ 
class StorageProfile extends BaseStorageProfile
{
	const STORAGE_SERVE_PRIORITY_KALTURA_ONLY = 1;
	const STORAGE_SERVE_PRIORITY_KALTURA_FIRST = 2;
	const STORAGE_SERVE_PRIORITY_EXTERNAL_FIRST = 3;
	const STORAGE_SERVE_PRIORITY_EXTERNAL_ONLY = 4;
	
	const STORAGE_STATUS_DISABLED = 1;
	const STORAGE_STATUS_AUTOMATIC = 2;
	const STORAGE_STATUS_MANUAL = 3;
	
	const STORAGE_KALTURA_DC = 0;
	const STORAGE_PROTOCOL_FTP = 1;
	const STORAGE_PROTOCOL_SCP = 2;
	const STORAGE_PROTOCOL_SFTP = 3;
	const STORAGE_PROTOCOL_S3 = 6;
	
	const STORAGE_DEFAULT_KALTURA_PATH_MANAGER = 'kPathManager';
	const STORAGE_DEFAULT_EXTERNAL_PATH_MANAGER = 'kExternalPathManager';
	
	const CUSTOM_DATA_URL_MANAGER_PARAMS = 'url_manager_params';
	const CUSTOM_DATA_PATH_MANAGER_PARAMS = 'path_manager_params';
	const CUSTOM_DATA_READY_BEHAVIOR = 'ready_behavior';
	const CUSTOM_DATA_RULES = 'rules';
	const CUSTOM_DATA_CREATE_FILE_LINK ='create_file_link';
	const CUSTOM_DATA_HTTPS_DELIVERY_URL ='https_delivery_url';
	
	/**
	 * @var kStorageProfileScope
	 */
	protected $scope;
	
	/**
	 * @return kPathManager
	 */
	
	public function getPathManager()
	{
		$class = $this->getPathManagerClass();
		if(!$class || !strlen(trim($class)) || !class_exists($class))
		{
			if($this->getProtocol() == self::STORAGE_KALTURA_DC)
			{
				$class = self::STORAGE_DEFAULT_KALTURA_PATH_MANAGER;
			}
			else
			{
				$class = self::STORAGE_DEFAULT_EXTERNAL_PATH_MANAGER;
			}
		}
			
		return new $class();
	}
	
	/* ---------------------------------- TODO - temp solution -----------------------------------------*/
	// remove after event manager implemented
	
	const STORAGE_TEMP_TRIGGER_MODERATION_APPROVED = 2;
	const STORAGE_TEMP_TRIGGER_FLAVOR_READY = 3;
		
	public function getTrigger() { return $this->getFromCustomData("trigger", null, self::STORAGE_TEMP_TRIGGER_FLAVOR_READY); }
	public function setTrigger( $v ) { $this->putInCustomData("trigger", (int)$v); }
	
	//external path format
	public function setPathFormat($v) { $this->putInCustomData('path_format', $v);}
	public function getPathFormat() { return $this->getFromCustomData('path_format', null);}
	
	/**
	 * 
	 * Get the allow_auto_delete parameter value
	 */
	public function getAllowAutoDelete() 
	{ 
		return (bool)$this->getFromCustomData("allow_auto_delete", null, false); 
	} // if not set to true explicitly, default will be false
	

	public function setAllowAutoDelete( $v )
	{ 
		$this->putInCustomData("allow_auto_delete", (bool)$v); 
	}
	
    public function setRTMPPrefix ($v)
	{
	    $this->putInCustomData("rtmp_prefix", $v);
	}
	
	public function getRTMPPrefix ()
	{
	    return $this->getFromCustomData("rtmp_prefix");
	}
	
    public function setRules ($v)
	{
	    $this->putInCustomData(self::CUSTOM_DATA_RULES, $v);
	}
	
	public function getRules ()
	{
	    return $this->getFromCustomData(self::CUSTOM_DATA_RULES);
	}
	
	public function getCreateFileLink ()
	{
	    return $this->getFromCustomData(self::CUSTOM_DATA_CREATE_FILE_LINK);
	}
	
    public function setCreateFileLink ($v)
	{
	    $this->putInCustomData(self::CUSTOM_DATA_CREATE_FILE_LINK, $v);
	}
	
	public function getDeliveryHttpsBaseUrl()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_HTTPS_DELIVERY_URL);
	}

	public function setDeliveryHttpsBaseUrl($v)
	{
		$this->putInCustomData(self::CUSTOM_DATA_HTTPS_DELIVERY_URL, $v);
	}
	
	/* ---------------------------------- TODO - temp solution -----------------------------------------*/
	
	/* URL Manager Params */
	
	public function setUrlManagerParams($params)
	{
	    $this->putInCustomData(self::CUSTOM_DATA_URL_MANAGER_PARAMS, serialize($params));
	}
	
	public function getUrlManagerParams()
	{
	    $params = $this->getFromCustomData(self::CUSTOM_DATA_URL_MANAGER_PARAMS);
	    $params = unserialize($params);
	    if (!$params) {
	        return array();
	    }
	    return $params;
	}
	
	/* Path Manager Params */
	
    public function setPathManagerParams($params)
	{
	    $this->putInCustomData(self::CUSTOM_DATA_PATH_MANAGER_PARAMS, serialize($params));
	}
	
	public function getPathManagerParams()
	{
	    $params = $this->getFromCustomData(self::CUSTOM_DATA_PATH_MANAGER_PARAMS);
	    $params = unserialize($params);
	    if (!$params) {
	        return array();
	    }
	    return $params;
	}
	
	
    public function setReadyBehavior($readyBehavior)
	{
	    $this->putInCustomData(self::CUSTOM_DATA_READY_BEHAVIOR, $readyBehavior);
	}
	
	public function getReadyBehavior()
	{
	    // return NO_IMPACT as default when no other value is set
	    return $this->getFromCustomData(self::CUSTOM_DATA_READY_BEHAVIOR, null, StorageProfileReadyBehavior::NO_IMPACT);
	}
	
	/* Cache Invalidation */
	
	public function getCacheInvalidationKeys()
	{
		return array("storageProfile:id=".strtolower($this->getId()), "storageProfile:partnerId=".strtolower($this->getPartnerId()));
	}
	
	/**
	 * @param flavorAsset $flavorAsset
	 * @return boolean true if the given flavor asset is configured to be exported or false otherwise
	 */
	public function shouldExportFlavorAsset(flavorAsset $flavorAsset)
	{
		KalturaLog::debug('Checking if flavor asset ['.$flavorAsset->getId().'] should be exported to ['.$this->getId().']');
		if(!$flavorAsset->isLocalReadyStatus())
		{
			KalturaLog::debug('Flavor is not ready for export');
			return false;
		}
					
		if(!$this->isFlavorAssetConfiguredForExport($flavorAsset))
		{
			KalturaLog::debug('Flavor asset is not configured for export');
			return false;
		}

		$scope = $this->getScope();
		$scope->setEntryId($flavorAsset->getEntryId());
		if(!$this->fulfillsRules($scope))
		{
			KalturaLog::debug('Storage profile export rules are not fulfilled');
			return false;
		}
			
		KalturaLog::debug('Flavor should be exported');
		return true;	    
	}
	
	public function shoudlExportFileSync(FileSyncKey $key)
	{
		if($this->isExported($key))
		{
			KalturaLog::debug('Flavor was already exported');
			return false;
		}
		if(!$this->isValidFileSync($key))
		{
			KalturaLog::debug('File sync is not valid for export');
			return false;
		}
		return true;	    
	}
	
	/**
	 * @return true if the profile's trigger fits a ready flavor asset for the given entry id
	 * @param string $entryId
	 */
	public function triggerFitsReadyAsset($entryId)
	{
	    if ($this->getTrigger() == StorageProfile::STORAGE_TEMP_TRIGGER_FLAVOR_READY) {
	        return true;
	    }
	    
	    if ($this->getTrigger() == StorageProfile::STORAGE_TEMP_TRIGGER_MODERATION_APPROVED) {
	        $entry = entryPeer::retrieveByPK($entryId);
	        if ($entry && $entry->getModerationStatus() == entry::ENTRY_MODERATION_STATUS_APPROVED) {
                return true;	            
	        }
	    }
	    return false;
	}
	
	
	public function isPendingExport(FileSyncKey $key)
	{
	    $c = FileSyncPeer::getCriteriaForFileSyncKey( $key );
		$c->addAnd(FileSyncPeer::DC, $this->getId(), Criteria::EQUAL);
		$fileSync = FileSyncPeer::doSelectOne($c);
		if (!$fileSync) {
		    return false;
		}
		return ($fileSync->getStatus() == FileSync::FILE_SYNC_STATUS_PENDING);
	}
	
	/**
	 * Validate if the entry should be exported to the remote storage according to the defined export rules
	 * 
	 * @param kStorageProfileScope $scope
	 */
	public function fulfillsRules(kStorageProfileScope $scope)
	{
		if(!PermissionPeer::isValidForPartner(PermissionName::FEATURE_REMOTE_STORAGE_RULE, $this->getPartnerId()))
			return true;
			
		if(!is_array($this->getRules()) || !count($this->getRules())) 
			return true;
			
		$context = null;
		if(!array_key_exists($this->getId(), kStorageExporter::$entryContextDataResult))
		{
			kStorageExporter::$entryContextDataResult[$this->getId()] = array();
		}
		
		if(array_key_exists($scope->getEntryId(), kStorageExporter::$entryContextDataResult[$this->getId()]))
		{
			KalturaLog::debug("Found rule->applyContext result in cache");
			$context = kStorageExporter::$entryContextDataResult[$this->getId()][$scope->getEntryId()];
		}
		else
		{	
			KalturaLog::debug("Validating rules");						
			$context = new kContextDataResult();	
			foreach ($this->getRules() as $rule) 
			{
				/* @var $rule kRule */
				$rule->setScope($scope);
				$fulfilled = $rule->applyContext($context);
				
				if($fulfilled && $rule->getStopProcessing())
					break;
			}
			kStorageExporter::$entryContextDataResult[$this->getId()][$scope->getEntryId()] = $context;
		}
		
		foreach ($context->getActions() as $action) 
		{
			/* @var $action kRuleAction */
			if($action->getType() == RuleActionType::ADD_TO_STORAGE)
				return true;
		}
		
		return false;
	}
	
	/**
	 * Check if input key was already exported for this storage profile
	 * 
	 * @param FileSyncKey $key
	 */
	public function isExported(FileSyncKey $key)
	{
		$storageFileSync = kFileSyncUtils::getReadyPendingExternalFileSyncForKey($key, $this->getId());

		if($storageFileSync) // already exported or currently being exported
		{
			KalturaLog::log(__METHOD__ . " key [$key] already exported or being exported");
			return true;
		}
		else 
		{
			return false;
		}
	}
	
	/**
	 * Check if flavor asset id set for export on the storage profile
	 * 
	 * @param flavorAsset $flavorAsset
	 */
	protected function isFlavorAssetConfiguredForExport(flavorAsset $flavorAsset)
	{
		$configuredForExport = null;
	    
	    // check if flavor params id is in the list to export
	    $flavorParamsIdsToExport = $this->getFlavorParamsIds();
	    KalturaLog::log(__METHOD__ . " flavorParamsIds [$flavorParamsIdsToExport]");
	    
	    if (is_null($flavorParamsIdsToExport) || strlen(trim($flavorParamsIdsToExport)) == 0)
	    {
	        // all flavor assets should be exported
	        $configuredForExport = true;
	    }
	    else
	    {
	        $flavorParamsIdsToExport = array_map('trim', explode(',', $flavorParamsIdsToExport));
	        if (in_array($flavorAsset->getFlavorParamsId(), $flavorParamsIdsToExport))
	        {
	            // flavor set to export
	            $configuredForExport = true;
	        }
	        else
	        {
	            // flavor not set to export
	            $configuredForExport = false;
	        }
	    }

	    return $configuredForExport;
	}
	
	public function isValidFileSync(FileSyncKey $key)
	{
		KalturaLog::log(__METHOD__ . " - key [$key], externalStorage id[" . $this->getId() . "]");
		
		list($kalturaFileSync, $local) = kFileSyncUtils::getReadyFileSyncForKey($key, false, false);
		if(!$kalturaFileSync) // no local copy to export from
		{
			KalturaLog::log(__METHOD__ . " key [$key] not found localy");
			return false;
		}
		
		KalturaLog::log(__METHOD__ . " validating file size [" . $kalturaFileSync->getFileSize() . "] is between min [" . $this->getMinFileSize() . "] and max [" . $this->getMaxFileSize() . "]");
		if($this->getMaxFileSize() && $kalturaFileSync->getFileSize() > $this->getMaxFileSize()) // too big
		{
			KalturaLog::log(__METHOD__ . " key [$key] file too big");
			return false;
		}
			
		if($this->getMinFileSize() && $kalturaFileSync->getFileSize() < $this->getMinFileSize()) // too small
		{
			KalturaLog::log(__METHOD__ . " key [$key] file too small");
			return false;
		}
			
		return true;
		
	}
	
	/**
	 * Get the storage profile scope
	 * 
	 * @return kStorageProfileScope
	 */
	public function &getScope()
	{
		if (!$this->scope)
		{
			$this->scope = new kStorageProfileScope();
			$this->scope->setStorageProfileId($this->getId());
		}
			
		return $this->scope;
	}
	
	/**
	 * Set the kStorageProfileScope, called internally only
	 * 
	 * @param $scope
	 */
	protected function setScope(kStorageProfileScope $scope)
	{
		$this->scope = $scope;
	}
}
