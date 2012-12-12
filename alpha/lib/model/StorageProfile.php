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
		return array("storageProfile:id=".$this->getId(), "storageProfile:partnerId=".$this->getPartnerId());
	}
	
	/**
	 * @param flavorAsset $flavorAsset
	 * @return boolean true if the given flavor asset is configured to be exported or false otherwise
	 */
	public function shouldExportFlavorAsset(flavorAsset $flavorAsset)
	{
	    $shouldExport = null;
	    
	    // check if flavor params id is in the list to export
	    $flavorParamsIdsToExport = $this->getFlavorParamsIds();
	    KalturaLog::log(__METHOD__ . " flavorParamsIds [$flavorParamsIdsToExport]");
	    
	    if (is_null($flavorParamsIdsToExport) || strlen(trim($flavorParamsIdsToExport)) == 0)
	    {
	        // all flavor assets should be exported
	        $shouldExport = true;
	    }
	    else
	    {
	        $flavorParamsIdsToExport = array_map('trim', explode(',', $flavorParamsIdsToExport));
	        if (in_array($flavorAsset->getFlavorParamsId(), $flavorParamsIdsToExport))
	        {
	            // flavor set to export
	            $shouldExport = true;
	        }
	        else
	        {
	            // flavor not set to export
	            $shouldExport = false;
	        }
	    }
	    
	    // check if flavor fits the export rules defined on the profile
	    if ($shouldExport)
	    {
	        $key = $flavorAsset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
	        $shouldExport = kStorageExporter::shouldExport($key, $this);
	        
	        if (!$shouldExport)
	        {
	            KalturaLog::log("no need to export key [$key] to externalStorage id[" . $this->getId() . "]");
	        }
	    }
	    
	    return $shouldExport;
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
	
	
	public function isPendingExport(asset $asset)
	{
	    $key = $asset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
	    $c = FileSyncPeer::getCriteriaForFileSyncKey( $key );
		$c->addAnd(FileSyncPeer::DC, $this->getId(), Criteria::EQUAL);
		$fileSync = FileSyncPeer::doSelectOne($c);
		if (!$fileSync) {
		    return false;
		}
		return ($fileSync->getStatus() == FileSync::FILE_SYNC_STATUS_PENDING);
	}
}
