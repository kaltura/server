<?php

/**
 * Subclass for representing a row from the 'flavor_asset' table.
 *
 * 
 *
 * @package Core
 * @subpackage model
 */ 
class asset extends Baseasset implements ISyncableFile, IRelatedObject
{
	/**
	 * @deprecated use ASSET_STATUS_ERROR instead
	 */
	const FLAVOR_ASSET_STATUS_ERROR = -1;
	
	/**
	* @deprecated use ASSET_STATUS_QUEUED instead
	*/
	const FLAVOR_ASSET_STATUS_QUEUED = 0;
	
	/**
	* @deprecated use ASSET_STATUS_CONVERTING instead
	*/
	const FLAVOR_ASSET_STATUS_CONVERTING = 1;
	
	/**
	* @deprecated use ASSET_STATUS_READY instead
	*/
	const FLAVOR_ASSET_STATUS_READY = 2;
	
	/**
	* @deprecated use ASSET_STATUS_DELETED instead
	*/
	const FLAVOR_ASSET_STATUS_DELETED = 3;
	
	/**
	* @deprecated use ASSET_STATUS_NOT_APPLICABLE instead
	*/
	const FLAVOR_ASSET_STATUS_NOT_APPLICABLE = 4;
	
	/**
	* @deprecated use ASSET_STATUS_TEMP instead
	*/
	const FLAVOR_ASSET_STATUS_TEMP = 5; // used during conversion and should be deleted
	
	/**
	* @deprecated use ASSET_STATUS_WAIT_FOR_CONVERT instead
	*/
	const FLAVOR_ASSET_STATUS_WAIT_FOR_CONVERT = 6; // can't convert since the source is not ready yet, will be converted when the source is ready
	
	/**
	* @deprecated use ASSET_STATUS_IMPORTING instead
	*/
	const FLAVOR_ASSET_STATUS_IMPORTING = 7;
	
	/**
	* @deprecated use ASSET_STATUS_VALIDATING instead
	*/
	const FLAVOR_ASSET_STATUS_VALIDATING = 8;
	
	const ASSET_STATUS_ERROR = -1;
	const ASSET_STATUS_QUEUED = 0;
	const ASSET_STATUS_CONVERTING = 1;
	const ASSET_STATUS_READY = 2;
	const ASSET_STATUS_DELETED = 3;
	const ASSET_STATUS_NOT_APPLICABLE = 4;
	const ASSET_STATUS_TEMP = 5; // used during conversion and should be deleted
	const ASSET_STATUS_WAIT_FOR_CONVERT = 6; // can't convert since the source is not ready yet, will be converted when the source is ready
	const ASSET_STATUS_IMPORTING = 7;
	const ASSET_STATUS_VALIDATING = 8;
	const ASSET_STATUS_EXPORTING = 9;
	
	/**
	 * @deprecated use FILE_SYNC_ASSET_SUB_TYPE_ASSET instead
	 */
	const FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET = 1;
	
	/**
	* @deprecated use FILE_SYNC_ASSET_SUB_TYPE_CONVERT_LOG instead
	*/
	const FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_CONVERT_LOG = 2;
	
	
	const FILE_SYNC_ASSET_SUB_TYPE_ASSET = 1;
	const FILE_SYNC_ASSET_SUB_TYPE_CONVERT_LOG = 2;
	const FILE_SYNC_ASSET_SUB_TYPE_ISM = 3;
	const FILE_SYNC_ASSET_SUB_TYPE_ISMC = 4;
	
	const FILE_SYNC_ASSET_SUB_TYPE_LIVE_PRIMARY = 5; 
	const FILE_SYNC_ASSET_SUB_TYPE_LIVE_SECONDARY = 6;

	const FILE_SYNC_ASSET_SUB_TYPE_MPD = 7;

	const CUSTOM_DATA_FIELD_PARTNER_DESCRIPTION = "partnerDescription";
	const CUSTOM_DATA_FIELD_PARTNER_DATA = "partnerData";
	const CUSTOM_DATA_FIELD_ACTUAL_SOURCE_ASSET_PARAMS_IDS = "actualSourceParamsIds";
	const CUSTOM_DATA_FILE_SYNC_VERSIONS_TO_DELETE = "fileSyncVersionsToDelete";
	const CUSTOM_DATA_PREVIOUS_VERSION = "previousVersion";
	const CUSTOM_DATA_ENCRYPTION_KEY = "encryptionKey";
	
	const MAX_ASSETS_PER_ENTRY = 500;
	
	public function copyToEntry($entryId = null, $partnerId = null)
	{
		$newFlavorAsset = $this->copy();
		//this is the first version of the new asset.
		$newFlavorAsset->incrementVersion();
		if($partnerId)
			$newFlavorAsset->setPartnerId($partnerId);
		if($entryId)
			$newFlavorAsset->setEntryId($entryId);
		$newFlavorAsset->save();
		
		$flavorParamsOutput = assetParamsOutputPeer::retrieveByAssetId($this->getId());
		if($flavorParamsOutput)
		{
			$newFlavorParamsOutput = $flavorParamsOutput->copy();
			$newFlavorParamsOutput->setPartnerId($newFlavorAsset->getPartnerId());
			$newFlavorParamsOutput->setEntryId($newFlavorAsset->getEntryId());
			$newFlavorParamsOutput->setFlavorAssetId($newFlavorAsset->getId());
			$newFlavorParamsOutput->save();
		}
		
		$mediaInfo = mediaInfoPeer::retrieveByFlavorAssetId($this->getId());
		if($mediaInfo)
		{
			$newMediaInfo = $mediaInfo->copy();
			$newMediaInfo->setFlavorAssetId($newFlavorAsset->getId());
			$newMediaInfo->setFlavorAssetVersion($newFlavorAsset->getVersion());
			$newMediaInfo->save();
		}
		
		$assetSyncKey = $this->getSyncKey(self::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
		$convertLogSyncKey = $this->getSyncKey(self::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_CONVERT_LOG);
		$ismSyncKey = $this->getSyncKey(self::FILE_SYNC_ASSET_SUB_TYPE_ISM);
		$ismcSyncKey = $this->getSyncKey(self::FILE_SYNC_ASSET_SUB_TYPE_ISMC);

		$newAssetSyncKey = $newFlavorAsset->getSyncKey(self::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
		$newConvertLogSyncKey = $newFlavorAsset->getSyncKey(self::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_CONVERT_LOG);
		$newIsmSyncKey = $newFlavorAsset->getSyncKey(self::FILE_SYNC_ASSET_SUB_TYPE_ISM);
		$newIsmcSyncKey = $newFlavorAsset->getSyncKey(self::FILE_SYNC_ASSET_SUB_TYPE_ISMC);

		if(kFileSyncUtils::fileSync_exists($assetSyncKey))
			kFileSyncUtils::softCopy($assetSyncKey, $newAssetSyncKey);

		if(kFileSyncUtils::fileSync_exists($convertLogSyncKey))
			kFileSyncUtils::softCopy($convertLogSyncKey, $newConvertLogSyncKey);
		
		if(kFileSyncUtils::fileSync_exists($ismSyncKey))
			kFileSyncUtils::softCopy($ismSyncKey, $newIsmSyncKey);
			
		if(kFileSyncUtils::fileSync_exists($ismcSyncKey))
			kFileSyncUtils::softCopy($ismcSyncKey, $newIsmcSyncKey);

		kEventsManager::raiseEvent(new kObjectAddedEvent($newFlavorAsset));
		
		return $newFlavorAsset;
	}
	
	public function linkFromAsset(asset $fromAsset)
	{
		$this->setWidth($fromAsset->getWidth());
		$this->setHeight($fromAsset->getHeight());
		$this->setContainerFormat($fromAsset->getContainerFormat());
		$this->setSize($fromAsset->getSize());
		$this->setFileExt($fromAsset->getFileExt());
		$this->setTags($fromAsset->getTags());
		$this->setDescription($fromAsset->getDescription());
		$encKey = $fromAsset->getEncryptionKey();
		if(isset($encKey))
			$this->setEncryptionKey($encKey);
		$this->incrementVersion();
		$this->setStatusLocalReady();		
	}
	
	public function save(PropelPDO $con = null)
	{
		if ($this->isNew())
		{
			$this->setId($this->calculateId());
		}
		return parent::save($con);
	}
	
	/* (non-PHPdoc)
	 * @see lib/model/om/BaseAsset#preInsert()
	 */
	public function preInsert(PropelPDO $con = null)
	{
		//Validate max assets limitation was not reached before doing insert
		$partner = PartnerPeer::retrieveByPK($this->getPartnerId());
		
		if($partner)
	  		$assetPerEntryLimitation = $partner->getAssetsPerEntryLimitation();
	  		
	  	if(!isset($assetPerEntryLimitation) || $assetPerEntryLimitation == false)
	    	$assetPerEntryLimitation = self::MAX_ASSETS_PER_ENTRY;
	    		
	  	$assetsCount = assetPeer::countByEntryId($this->entry_id);
	  		
	  	if($assetsCount+1 > $assetPerEntryLimitation)
	    	throw new kCoreException("Max number of allowed assets per entry was reached", kCoreException::MAX_ASSETS_PER_ENTRY);
	    	
	    return parent::preInsert();
	}

	/* (non-PHPdoc)
	 * @see lib/model/om/BaseAsset#postInsert()
	 */
	public function postInsert(PropelPDO $con = null)
	{
		$status = $this->getStatus();

		$ret = parent::postInsert( $con );
		
		if ( $status == flavorAsset::ASSET_STATUS_READY )
		{
			$this->onAssetContentModified();
		}
		
		return $ret;
	}
	
	/* (non-PHPdoc)
	 * @see lib/model/om/BaseflavorAsset#postUpdate()
	 */
	public function postUpdate(PropelPDO $con = null)
	{
		if ($this->alreadyInSave)
			return parent::postUpdate($con);
		
		$statusChangedToReady = false;
		if ( $this->isColumnModified(assetPeer::STATUS) && ($this->getStatus() == self::FLAVOR_ASSET_STATUS_READY) )
		{
			$statusChangedToReady = true;
		}
		
		$versionModified = false;
		if ( $this->isColumnModified(assetPeer::VERSION ) )
		{
			$versionModified = true;
		}

		$objectDeleted = false;
		if(
			($this->isColumnModified(assetPeer::STATUS) && $this->getStatus() == self::FLAVOR_ASSET_STATUS_DELETED)
			||
			($this->isColumnModified(assetPeer::DELETED_AT) && !is_null($this->getDeletedAt(null)))
		)
			$objectDeleted = true;
			
		$ret = parent::postUpdate($con);
		
		if($objectDeleted)
			kEventsManager::raiseEvent(new kObjectDeletedEvent($this));
			
		if ( $statusChangedToReady || $versionModified || $objectDeleted )
		{
			$this->onAssetContentModified();
		}
		
		return $ret;
	}

	protected function onAssetContentModified()
	{

	}
		
	public function incrementVersion()
	{
		if(isset($this->version))
			$this->setPreviousVersion($this->version);
		$newVersion = kFileSyncUtils::calcObjectNewVersion($this->getId(), $this->getVersion(), FileSyncObjectType::ASSET, asset::FILE_SYNC_ASSET_SUB_TYPE_ASSET);
		
		$this->setVersion($newVersion);
	}
	
	public function addTags(array $newTags)
	{
		$tags = $this->getTagsArray();
		foreach($newTags as $newTag)
			if(!in_array($newTag, $tags))
				$tags[] = $newTag;
				
		$this->setTagsArray($tags);
	}
	
	public function removeTags(array $tagsToRemove)
	{
		$tags = $this->getTagsArray();
		$newTags = array();
		foreach($tags as $tag)
			if(!in_array($tag, $tagsToRemove))
				$newTags[] = $tag;
				
		$this->setTagsArray($newTags);
	}
	
	
	protected static function validateFileSyncSubType ( $sub_type )
	{
		$valid_sub_types = array(
			self::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET,
			self::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_CONVERT_LOG,
			self::FILE_SYNC_ASSET_SUB_TYPE_ISM,
			self::FILE_SYNC_ASSET_SUB_TYPE_ISMC,
			self::FILE_SYNC_ASSET_SUB_TYPE_MPD,
		);
		if (!in_array($sub_type, $valid_sub_types))
			throw new FileSyncException(FileSyncObjectType::FLAVOR_ASSET, $sub_type, $valid_sub_types);		
	}
	
	protected function getVersionForSubType($sub_type, $version = null)
	{
		switch ($sub_type)
		{
			case asset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET:
			case asset::FILE_SYNC_ASSET_SUB_TYPE_ISM:
			case asset::FILE_SYNC_ASSET_SUB_TYPE_ISMC:
			case asset::FILE_SYNC_ASSET_SUB_TYPE_MPD:
				return $this->getVersion();
				
			case asset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_CONVERT_LOG:
				return $this->getLogFileVersion();
		}
		return null;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see lib/model/ISyncableFile#getSyncKey()
	 */
	public function getSyncKey($sub_type, $version = null)
	{
		static::validateFileSyncSubType($sub_type);
		$key = new FileSyncKey();
		$key->object_type = FileSyncObjectType::FLAVOR_ASSET;
		$key->object_sub_type = $sub_type;
		$key->object_id = $this->getId();
		if ($version)
		{
			$key->version = $version;
		}
		else
		{
			$key->version = $this->getVersionForSubType($sub_type);
		}
		$key->partner_id = $this->getPartnerId();
		
		return $key;
	}

	
	
	/* (non-PHPdoc)
	 * @see lib/model/ISyncableFile#generateFileName()
	 */
	public function generateFileName( $sub_type, $version = null)
	{
		static::validateFileSyncSubType ( $sub_type );
		
		$entry = $this->getentry();
		if(!$entry)
			return null;
			
		$fileName = $entry->getId() . "_" . $this->getId() . "_$version";
				 
		switch($sub_type)
		{
			case self::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET:
				$ext = '';
				if($this->hasTag(assetParams::TAG_ISM_MANIFEST))
					$ext = ".ism";
				elseif($this->hasTag(assetParams::TAG_SMIL_MANIFEST))
					$ext = ".smil";
				else if($this->getFileExt())
					$ext = '.' . $this->getFileExt();
				return $fileName . $ext;
				
			case self::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_CONVERT_LOG:
				return "$fileName.conv.log";
				
			case self::FILE_SYNC_ASSET_SUB_TYPE_ISM:
				return "$fileName.ism";
				
			case self::FILE_SYNC_ASSET_SUB_TYPE_ISMC:
				return "$fileName.ismc";
		}
		
		return null;
	}

	/**
	 * (non-PHPdoc)
	 * @see lib/model/ISyncableFile#generateFilePathArr()
	 */
	public function generateFilePathArr($sub_type, $version = null)
	{
		static::validateFileSyncSubType ( $sub_type );
		$version = (is_null($version) ? $this->getVersionForSubType($sub_type) : $version);
		
		$entry = entryPeer::retrieveByPKNoFilter($this->getEntryId());
		if(!$entry)
			throw new Exception("Could not find entry [" . $this->getEntryId() . "] for asset [" . $this->getId() . "]");
		
		$dir = (intval($entry->getIntId() / 1000000)).'/'.	(intval($entry->getIntId() / 1000) % 1000);
		$path =  "/content/entry/data/$dir/" . $this->generateFileName($sub_type, $version);

		return array(myContentStorage::getFSContentRootPath(), $path); 
	}
	
	/**
	 * @var FileSync
	 */
	private $m_file_sync;
	
	/* (non-PHPdoc)
	 * @see lib/model/ISyncableFile#getFileSync()
	 */
	public function getFileSync ( )
	{
		return $this->m_file_sync; 
	}
	
	/* (non-PHPdoc)
	 * @see lib/model/ISyncableFile#setFileSync()
	 */
	public function setFileSync ( FileSync $file_sync )
	{
		 $this->m_file_sync = $file_sync;
	}
	
	/**
	 * Set the value of [file_ext] column.
	 * 
	 * @param      string $v new value
	 * @return     asset The current object (for fluent API support)
	 */
	public function setFileExt($v)
	{
		$v = trim($v);
		if (preg_match('/[\s\t\n\r]/', $v)){
			preg_match('/\w*/', $v, $v);
			KalturaLog::err("File extension cannot contain spaces, saving only ".$v[0]);
			parent::setFileExt($v[0]);
		}
		else{
			parent::setFileExt($v);
		}
	}
	
	private function calculateId()
	{
		$dc = kDataCenterMgr::getCurrentDc();
		for ($i = 0; $i < 10; $i++)
		{
			$id = $dc["id"].'_'.kString::generateStringId();
			$existingObject = assetPeer::retrieveByIdNoFilter($id);
			
			if ($existingObject)
				KalturaLog::log(__METHOD__ . ": id [$id] already exists");
			else
				return $id;
		}
		
		throw new Exception("Could not find unique id for flavorAsset");
	}

	public function getFormat()
	{
		$assetParams = $this->getassetParams();
		if ($assetParams)
			return $assetParams->getFormat();
		else
			return null;
	}
	
	public function getExternalUrl($storageId, $fileName = null)
	{
		$key = $this->getSyncKey(self::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
		$fileSync = kFileSyncUtils::getReadyExternalFileSyncForKey($key, $storageId);
		if(!$fileSync || $fileSync->getStatus() != FileSync::FILE_SYNC_STATUS_READY)
			return null;
		
		$storage = StorageProfilePeer::retrieveByPK($fileSync->getDc());
		if(!$storage)
			return null;
			
		$urlManager = DeliveryProfilePeer::getRemoteDeliveryByStorageId(DeliveryProfileDynamicAttributes::init($fileSync->getDc(), $this->getEntryId()), $fileSync, $this);
		if(is_null($urlManager)) 
			return null;
			
		$url = ltrim($urlManager->getFileSyncUrl($fileSync), "/");
		if (strpos($url, "://") === false){
			$url = rtrim($urlManager->getUrl(), "/") . "/".$url ;
		}
		
		$url = $this->finalizeDownloadUrl($fileSync, $url, $fileName, true);
		
		return $url;
	}
	
	public function getDownloadUrl($useCdn = false, $forceProxy = false, $preview = null, $fileName = null)
	{
		$syncKey = $this->getSyncKey(self::FILE_SYNC_ASSET_SUB_TYPE_ASSET);
		
		$fileSync = null;
		$serveRemote = false;
		$partner = PartnerPeer::retrieveByPK($this->getPartnerId());
		
		switch($partner->getStorageServePriority())
		{
			case StorageProfile::STORAGE_SERVE_PRIORITY_EXTERNAL_ONLY:
				$serveRemote = true;
				$fileSync = kFileSyncUtils::getReadyPendingExternalFileSyncForKey($syncKey);
				if(!$fileSync)
				{
					throw new kCoreException("File sync not found: $syncKey", kCoreException::FILE_NOT_FOUND);
				} 
				else if ($fileSync->getStatus() != FileSync::FILE_SYNC_STATUS_READY)
				{
					throw new kCoreException("File sync is pending: $syncKey",kCoreException::FILE_PENDING);
				}
				break;
			
			case StorageProfile::STORAGE_SERVE_PRIORITY_EXTERNAL_FIRST:
				$fileSync = kFileSyncUtils::getReadyExternalFileSyncForKey($syncKey);
				if($fileSync && $fileSync->getStatus() == FileSync::FILE_SYNC_STATUS_READY)
				{
					$serveRemote = true;
					break;
				}
			
			case StorageProfile::STORAGE_SERVE_PRIORITY_KALTURA_ONLY:
				$fileSync = kFileSyncUtils::getReadyInternalFileSyncForKey($syncKey);
				if(!$fileSync)
				    throw new kCoreException("File sync not found: $syncKey", kCoreException::FILE_NOT_FOUND);
				
				break;
			
			case StorageProfile::STORAGE_SERVE_PRIORITY_KALTURA_FIRST:
				$fileSync = kFileSyncUtils::getReadyInternalFileSyncForKey($syncKey);
				if($fileSync)
					break;
					
				$fileSync = kFileSyncUtils::getReadyExternalFileSyncForKey($syncKey);
				if(!$fileSync || $fileSync->getStatus() != FileSync::FILE_SYNC_STATUS_READY)
					throw new kCoreException("File sync not found: $syncKey", kCoreException::FILE_NOT_FOUND);
				
				$serveRemote = true;
				break;
		}
		
		if($serveRemote && $fileSync) {
			$downloadUrl = $fileSync->getExternalUrl($this->getEntryId());
		}
		else {
		    $downloadUrl = $this->getDownloadUrlWithExpiry(86400, $useCdn, $forceProxy, $preview);
		}
		
		$downloadUrl = $this->finalizeDownloadUrl($fileSync, $downloadUrl, $fileName, $serveRemote);
		
		return $downloadUrl;
	}
	
	public function finalizeDownloadUrl($fileSync, $url, $fileName = null, $serveRemote = false)
	{
	    if($fileSync->getIsDir() && $fileName)
	    {
	        if($serveRemote)
	            $url .= "/" . $fileName;
	        else
	            $url .= "/file_name/" . $fileName;
	    }
	    
	    return $url;
	}
	
	public function isKsNeededForDownload()
	{
		$entry = $this->getentry();
		if(!$entry)
			return true;
		
		if (PermissionPeer::isValidForPartner(PermissionName::FEATURE_ENTITLEMENT, $this->getPartnerId()))
			return true;
		
		return $entry->isSecuredEntry();
	}
	
	public function getDownloadUrlWithExpiry($expiry, $useCdn = false, $forceProxy = false, $preview = null)
	{
		$ksStr = "";
		$partnerId = $this->getPartnerId();
		
		if ($this->isKsNeededForDownload() || $preview)
		{
			$partner = PartnerPeer::retrieveByPK($partnerId);
			$secret = $partner->getSecret();
			$privilege = ks::PRIVILEGE_DOWNLOAD.":".$this->getEntryId();
			$privilege .= ",".kSessionBase::PRIVILEGE_DISABLE_ENTITLEMENT_FOR_ENTRY .":". $this->getEntryId();
			$privilege .= "," . kSessionBase::PRIVILEGE_VIEW . ":" . $this->getEntryId();       
			$privilege .= "," . kSessionBase::PRIVILEGE_DOWNLOAD_ASSET . ":" . $this->getId();
			
			if($preview)
				$privilege .= "," . kSessionBase::PRIVILEGE_PREVIEW . ":" . $preview;

			$result = kSessionUtils::startKSession($partnerId, $secret, null, $ksStr, $expiry, false, "", $privilege);
	
			if ($result < 0)
				throw new Exception("Failed to generate session for asset [".$this->getId()."] of type ". $this->getType());
		}
		
		$finalPath = $this->getFinalDownloadUrlPathWithoutKs();
		
		if ($ksStr)
			$finalPath .= "/ks/".$ksStr;
		
		if ($forceProxy)
			$finalPath .= "/relocate/".$this->getEntryId().".".$this->getFileExt();
		// Gonen May 12 2010 - removing CDN URLs. see ticket 5135 in internal mantis
		// in order to avoid conflicts with access_control (geo-location restriction), we always return the requestHost (www_host from kConf)
		// and not the CDN host relevant for the partner.
		
		// Tan-Tan January 27 2011 - in some places we do need the cdn, I added a paramter useCdn to force it.
		if($useCdn)
		{
			// TODO in that case we should use the serve flavor and the url manager in order to support secured and signed urls
			$downloadUrl = myPartnerUtils::getCdnHost($partnerId) . $finalPath;
		}
		else
			$downloadUrl = requestUtils::getRequestHost() . $finalPath;
		
		return $downloadUrl;
	}
	
	public function getFinalDownloadUrlPathWithoutKs()
	{
		$finalPath = myPartnerUtils::getUrlForPartner($this->getPartnerId(),$this->getPartnerId()*100).
					"/download".
					"/entry_id/".$this->getEntryId().
					"/flavor/".$this->getId();
		
		return $finalPath;
	}
	
	public function hasTag($v)
	{
		$tags = explode(',', $this->getTags());
		return in_array($v, $tags);
	}
	
	public function setTagsArray(array $tags)
	{
		$this->setTags(implode(',', $tags));
	}
	
	public function getTagsArray()
	{
		if(!strlen(trim($this->getTags())))
			return array();
			
		return explode(',', $this->getTags());
	}
	
	public function getTags()
	{
		return trim(parent::getTags());
	}

	/**
	 * @return flavorParamsOutput
	 */
	public function getFlavorParamsOutput()
	{
		return assetParamsOutputPeer::retrieveByAsset($this);
	}
	
	public function getLogFileVersion()
	{
		return $this->getFromCustomData("logFileVersion", null, kDataCenterMgr::incrementVersion());
	}
	
	public function incLogFileVersion()
	{
		$newVersion = kFileSyncUtils::calcObjectNewVersion($this->getId(), $this->getLogFileVersion(), FileSyncObjectType::ASSET, asset::FILE_SYNC_ASSET_SUB_TYPE_CONVERT_LOG);
		$this->putInCustomData("logFileVersion", $newVersion);
	}

	public function getCacheInvalidationKeys()
	{
		return array("flavorAsset:id=".strtolower($this->getId()), "flavorAsset:entryId=".strtolower($this->getEntryId()));
	}
	
	public function getPartnerDescription()			{return $this->getFromCustomData(self::CUSTOM_DATA_FIELD_PARTNER_DESCRIPTION);}
	public function setPartnerDescription($v)		{$this->putInCustomData(self::CUSTOM_DATA_FIELD_PARTNER_DESCRIPTION, $v);}
	
	public function getPartnerData()		{return $this->getFromCustomData(self::CUSTOM_DATA_FIELD_PARTNER_DATA);}
	public function setPartnerData($v)		{$this->putInCustomData(self::CUSTOM_DATA_FIELD_PARTNER_DATA, $v);}

	public function getPreviousVersion()	{return $this->getFromCustomData(self::CUSTOM_DATA_PREVIOUS_VERSION);}
	public function setPreviousVersion($v)	{$this->putInCustomData(self::CUSTOM_DATA_PREVIOUS_VERSION, $v);}

	public function setFromAssetParams($dbAssetParams)
	{
		$this->setContainerFormat($dbAssetParams->getFormat());
		$this->setHeight($dbAssetParams->getHeight());
		$this->setWidth($dbAssetParams->getWidth());
		$this->addTags($dbAssetParams->getTagsArray());
	}
	
	/**
	 * @return array of asset status values that mean the asset is at post conversion status (ready locally)
	 * Can be overwritten for specific asset types
	 */
	public function isLocalReadyStatus()
	{
		$status = $this->getStatus();
	    if($status == asset::ASSET_STATUS_EXPORTING || $status == asset::ASSET_STATUS_READY)
	    	return true;
	    	
	    return false;
	}

	public function getName()
	{
		return $this->getId();
	}
	
    /**
     * Set the asset status to a locally ready status (READY, EXPORTING) according to the required jobs to perform on the asset
     */
    public function setStatusLocalReady()
	{
	    parent::setStatus(asset::ASSET_STATUS_READY);
	}
	
	public function getActualSourceAssetParamsIds()		{return $this->getFromCustomData(self::CUSTOM_DATA_FIELD_ACTUAL_SOURCE_ASSET_PARAMS_IDS);}
	public function setActualSourceAssetParamsIds($v)	{$this->putInCustomData(self::CUSTOM_DATA_FIELD_ACTUAL_SOURCE_ASSET_PARAMS_IDS, $v);}

	public function getEncryptionKey()			{return $this->getFromCustomData(self::CUSTOM_DATA_ENCRYPTION_KEY);}
	public function setEncryptionKey($v)			{$this->putInCustomData(self::CUSTOM_DATA_ENCRYPTION_KEY, $v);}
	
	public function resetFileSyncVersionsToDelete() 	{$this->putInCustomData(self::CUSTOM_DATA_FILE_SYNC_VERSIONS_TO_DELETE, null);}
	public function getFileSyncVersionsToDelete()		{return $this->getFromCustomData(self::CUSTOM_DATA_FILE_SYNC_VERSIONS_TO_DELETE);}
	public function setFileSyncVersionsToDelete($v)	
	{
		$versionsToDelete = $this->getFileSyncVersionsToDelete();
		
		if($versionsToDelete)
			$v = array_merge($versionsToDelete, $v);
		
		$this->putInCustomData(self::CUSTOM_DATA_FILE_SYNC_VERSIONS_TO_DELETE, $v);
	}

	public function getentry(PropelPDO $con = null)
	{
		return entryPeer::retrieveByPk($this->entry_id);
	}
	
	public function getassetParams(PropelPDO $con = null)
	{
		return assetParamsPeer::retrieveByPk($this->flavor_params_id);
	}
	
	public function shouldCopyOnReplacement() {return true;}
		
}
