<?php

/**
 * Subclass for representing a row from the 'flavor_asset' table.
 *
 * 
 *
 * @package lib.model
 */ 
class flavorAsset extends BaseflavorAsset implements ISyncableFile
{
	const FLAVOR_ASSET_STATUS_ERROR = -1;
	const FLAVOR_ASSET_STATUS_QUEUED = 0;
	const FLAVOR_ASSET_STATUS_CONVERTING = 1;
	const FLAVOR_ASSET_STATUS_READY = 2;
	const FLAVOR_ASSET_STATUS_DELETED = 3;
	const FLAVOR_ASSET_STATUS_NOT_APPLICABLE = 4;
	const FLAVOR_ASSET_STATUS_TEMP = 5; // used during conversion and should be deleted
	
	const FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET = 1;
	const FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_CONVERT_LOG = 2;
	
	public function copyToEntry($entryId = null, $partnerId = null)
	{
		$newFlavorAsset = $this->copy();
		
		if($partnerId)
			$newFlavorAsset->setPartnerId($partnerId);
		if($entryId)
			$newFlavorAsset->setEntryId($entryId);
		$newFlavorAsset->save();
		
		$flavorParamsOutput = flavorParamsOutputPeer::retrieveByFlavorAssetId($this->getId());
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
			$newMediaInfo->save();
		}
		
		$assetSyncKey = $this->getSyncKey(self::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
		$convertLogSyncKey = $this->getSyncKey(self::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_CONVERT_LOG);
		
		$newAssetSyncKey = $newFlavorAsset->getSyncKey(self::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
		$newConvertLogSyncKey = $newFlavorAsset->getSyncKey(self::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_CONVERT_LOG);
		
		if(kFileSyncUtils::file_exists($assetSyncKey, true))
			kFileSyncUtils::softCopy($assetSyncKey, $newAssetSyncKey);
			
		if(kFileSyncUtils::file_exists($convertLogSyncKey, true))
			kFileSyncUtils::softCopy($convertLogSyncKey, $newConvertLogSyncKey);
		
		return $newFlavorAsset;
	}
	
	public function save(PropelPDO $con = null)
	{
		if ($this->isNew())
		{
			$this->setId($this->calculateId());
		}
		parent::save($con);
	}

	/* (non-PHPdoc)
	 * @see lib/model/om/BaseflavorAsset#preUpdate()
	 */
	public function preUpdate(PropelPDO $con = null)
	{
		if(
			($this->isColumnModified(flavorAssetPeer::STATUS) && $this->getStatus() == self::FLAVOR_ASSET_STATUS_DELETED)
			||
			($this->isColumnModified(flavorAssetPeer::DELETED_AT) && !is_null($this->getDeletedAt(null)))
		)
			kEventsManager::raiseEvent(new kObjectDeletedEvent($this));
			
		return parent::preUpdate($con);
	}
	
	public function incrementVersion()
	{
		$version = $this->getVersion();
		$this->setVersion(is_null($version) ? 1 : $version + 1);
	}
	
	public function addTags(array $newTags)
	{
		$tags = $this->getTagsArray();
		foreach($newTags as $newTag)
			if(!in_array($newTag, $tags))
				$tags[] = $newTag;
				
		$this->setTagsArray($tags);
	}
	
	
	private static function validateFileSyncSubType ( $sub_type )
	{
		$valid_sub_types = array(
			self::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET,
			self::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_CONVERT_LOG,
		);
		if (!in_array($sub_type, $valid_sub_types))
			throw new FileSyncException(FileSync::FILE_SYNC_OBJECT_TYPE_FLAVOR_ASSET, $sub_type, $valid_sub_types);		
	}
	
	/**
	 * (non-PHPdoc)
	 * @see lib/model/ISyncableFile#getSyncKey()
	 */
	public function getSyncKey($sub_type, $version = null)
	{
		self::validateFileSyncSubType($sub_type);
		$key = new FileSyncKey();
		$key->object_type = FileSync::FILE_SYNC_OBJECT_TYPE_FLAVOR_ASSET;
		$key->object_sub_type = $sub_type;
		$key->object_id = $this->getId();
		if ($version)
		{
			$key->version = $version;
		}
		else
		{
			switch ($sub_type)
			{
				case flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET:
					$key->version = $this->getVersion();
					break;
				case flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_CONVERT_LOG:
					$key->version = $this->getLogFileVersion();
					break;
			}
		}
		$key->partner_id = $this->getPartnerId();
		
		return $key;
	}

	
	
	/* (non-PHPdoc)
	 * @see lib/model/ISyncableFile#generateFileName()
	 */
	public function generateFileName( $sub_type, $version = null)
	{
		self::validateFileSyncSubType ( $sub_type );
		
		$entry = $this->getentry();
		$fileName = $entry->getId() . "_" . $this->getId() . "_$version";
				 
		switch($sub_type)
		{
			case self::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET:
				$ext = '';
				if($this->getFileExt())
					$ext = '.' . $this->getFileExt();
				return $fileName . $ext;
				
			case self::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_CONVERT_LOG:
				return "$fileName.conv.log";
		}
		
		return null;
	}

	/**
	 * (non-PHPdoc)
	 * @see lib/model/ISyncableFile#generateFilePathArr()
	 */
	public function generateFilePathArr($sub_type, $version = null)
	{
		self::validateFileSyncSubType ( $sub_type );
		$version = (is_null($version) ? $this->getVersion() : $version);
		
		$entry = $this->getentry();
		
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
	
	private function calculateId()
	{
		$dc = kDataCenterMgr::getCurrentDc();
		for ($i = 0; $i < 10; $i++)
		{
			$id = $dc["id"].'_'.kString::generateStringId();
			$existingObject = flavorAssetPeer::retrieveById($id);
			
			if ($existingObject)
				KalturaLog::log(__METHOD__ . ": id [$id] already exists");
			else
				return $id;
		}
		
		throw new Exception("Could not find unique id for flavorAsset");
	}

	public function getFormat()
	{
		$flavorParams = $this->getflavorParams();
		if ($flavorParams)
			return $flavorParams->getFormat();
		else
			return null;
	}
	
	public function getDownloadUrl()
	{
		return $this->getDownloadUrlWithExpiry(86400);
	}
	
	public function getDownloadUrlWithExpiry($expiry)
	{
		$ksStr = "";
		$partnerId = $this->getPartnerId();
		$partner = PartnerPeer::retrieveByPK($partnerId);
		$secret = $partner->getSecret();
		$privilege = ks::PRIVILEGE_DOWNLOAD.":".$this->getEntryId();
		$result = kSessionUtils::startKSession($partnerId, $secret, null, $ksStr, $expiry, false, "", $privilege);

		if ($result < 0)
			throw new Exception("Failed to generate session for flavor asset [".$this->getId()."]");
		
		$finalPath = myPartnerUtils::getUrlForPartner($this->getPartnerId(),$this->getPartnerId()*100).
			"/download".
			"/entry_id/".$this->getEntryId().
			"/flavor/".$this->getId().
			"/ks/".$ksStr;
			
		// Gonen May 12 2010 - removing CDN URLs. see ticket 5135 in internal mantis
		// in order to avoid conflicts with access_control (geo-location restriction), we always return the requestHost (www_host from kConf)
		// and not the CDN host relevant for the partner.
		$downloadUrl = requestUtils::getRequestHost().$finalPath;
		
		return $downloadUrl;
	}
	
	public function hasTag($v)
	{
		$tags = explode(',', $this->getTags());
		return in_array($v, $tags);
	}
	
	public function getIsWeb()
	{
		return $this->hasTag(flavorParams::TAG_WEB);
	}
	
	public function setTagsArray(array $tags)
	{
		$this->setTags(implode(',', $tags));
	}
	
	public function getTagsArray()
	{
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
		return flavorParamsOutputPeer::retrieveByFlavorAsset($this);
	}
	
	public function getLogFileVersion()
	{
		return $this->getFromCustomData("logFileVersion", null, 0);
	}
	
	public function incLogFileVersion()
	{
		$this->incInCustomData("logFileVersion", 1);
	}
}
