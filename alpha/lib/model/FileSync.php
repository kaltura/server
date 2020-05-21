<?php

/**
 * Subclass for representing a row from the 'file_sync' table.
 *
 * 
 *
 * @package Core
 * @subpackage model
 */ 
class FileSync extends BaseFileSync implements IBaseObject
{
	const FILE_SYNC_FILE_TYPE_FILE = 1;
	const FILE_SYNC_FILE_TYPE_LINK = 2;
	const FILE_SYNC_FILE_TYPE_URL = 3;
	const FILE_SYNC_FILE_TYPE_CACHE = 4;
	
	const FILE_SYNC_STATUS_ERROR = -1;
	const FILE_SYNC_STATUS_PENDING = 1;
	const FILE_SYNC_STATUS_READY = 2;
	const FILE_SYNC_STATUS_DELETED = 3;
	const FILE_SYNC_STATUS_PURGED = 4;

	private $statusMap = array (
		self::FILE_SYNC_STATUS_ERROR => "Error",
		self::FILE_SYNC_STATUS_PENDING => "Pending", 
		self::FILE_SYNC_STATUS_READY => "Ready",
		self::FILE_SYNC_STATUS_DELETED => "Deleted"
	);
	/**
	 * 
	 * @param FileSyncKey $key
	 * @return FileSync
	 */
	public static function createForFileSyncKey ( FileSyncKey $key )
	{
		$file_sync = new FileSync();
		$file_sync->setObjectId ( $key->object_id );
		$file_sync->setObjectType ( $key->object_type );
		$file_sync->setObjectSubType ( $key->object_sub_type );
		$file_sync->setVersion ( $key->version );
		if ( $key->partner_id ) $file_sync->setPartnerId ( $key->partner_id );
		return $file_sync;
	}

	public static function createFileSyncsPath(&$fileSyncs)
	{
		// make sure all file syncs have a path
		foreach ($fileSyncs as $fileSync)
		{
			$fileSync->createPath();
		}
	}

	public function createPath()
	{
		if ($this->getFileRoot() && $this->getFilePath())
		{
			return;
		}

		$fileSyncKey = kFileSyncUtils::getKeyForFileSync($this);
		list($fileRoot, $realPath) = kPathManager::getFilePathArr($fileSyncKey);

		$this->setFileRoot($fileRoot);
		$this->setFilePath($realPath);
	}

	public static function getFileSyncsChunkNoCriteria($baseCriteria, $fromId = 0, $toId = 0)
	{
		$c = clone $baseCriteria;
		
		if($toId)
		{
			$idCriterion = $c->getNewCriterion(FileSyncPeer::ID, $fromId, Criteria::GREATER_THAN);
			$idCriterion->addAnd($c->getNewCriterion(FileSyncPeer::ID, $toId, Criteria::LESS_EQUAL));
			$c->addAnd($idCriterion);
		}

		// Note: disabling the criteria because it accumulates more and more criterions, and the status was already explicitly added
		// once that bug is fixed, this can be removed
		FileSyncPeer::setUseCriteriaFilter(false);
		$fileSyncs = FileSyncPeer::doSelect($c, myDbHelper::getConnection(myDbHelper::DB_HELPER_CONN_PROPEL2));
		FileSyncPeer::setUseCriteriaFilter(true);

		return $fileSyncs;
	}

	protected static function getLockedFileSyncs($fileSyncs, $lockCache, $lockKeyPrefix)
	{
		// get locked file syncs with multi get
		$lockKeys = array();
		foreach ($fileSyncs as $fileSync)
		{
			$lockKeys[] = $lockKeyPrefix . $fileSync->getId();
		}

		$lockKeys = $lockCache->get($lockKeys);
		return $lockKeys;
	}

	public static function lockFileSyncs($fileSyncs, $lockCache, $lockKeyPrefix, $lockExpiryTimeOut, &$lockedFileSyncs,
	                                     &$limitReached = null, $maxCount = PHP_INT_MAX, &$maxSize = PHP_INT_MAX, &$lastId = null)
	{
		// Get lock file syncs
		$lockKeys = self::getLockedFileSyncs($fileSyncs, $lockCache, $lockKeyPrefix);

		// try to lock file syncs
		foreach ($fileSyncs as $fileSync)
		{
			$curKey = $lockKeyPrefix . $fileSync->getId();

			if (isset($lockKeys[$curKey]))
			{
				KalturaLog::info('file sync '.$fileSync->getId().' already locked');
				continue;
			}

			if (!$lockCache->add($curKey, true, $lockExpiryTimeOut))
			{
				KalturaLog::info('failed to lock file sync '.$fileSync->getId());
				continue;
			}

			KalturaLog::info('locked file sync ' . $fileSync->getId());

			// add to the result set
			$lockedFileSyncs[] = $fileSync;

			if($limitReached !== null)
			{
				$maxSize -= $fileSync->getFileSize();

				// check limit
				if ((count($lockedFileSyncs) >= $maxCount) || ($maxSize < 0))
				{
					if($lastId !== null)
					{
						$lastId = $fileSync->getId();
					}

					$limitReached = true;
					break;
				}
			}
		}
	}

	public function deleteLocalSiblings()
	{
		KalturaLog::info("Delete siblings for file sync [{$this->getObjectId()}] with ID [{$this->getId()}]");

		$fileSyncKey = kFileSyncUtils::getKeyForFileSync($this);
		kFileSyncUtils::deleteSyncFileForKey($fileSyncKey, false, true);
	}

	private function generateKey()
	{
		return implode("_", array($this->getObjectId(), $this->getObjectType(), $this->getObjectSubType()));
	}

	public function encrypt()
	{
		if (!$this->shouldEncryptFile())
			return;

		$this->setEncryptionKey($this->generateKey());
		$this->save();

		$key = $this->getEncryptionKey();
		$realPath = realpath($this->getFullPath());
		KalturaLog::debug("Encrypting content of fileSync " . $this->id . ". key is: [$key] in path [$realPath]");
		kEncryptFileUtils::encrypt($realPath, $key, $this->getIv());
	}

	public function decrypt()
	{
		$realPath = realpath($this->getFullPath());
		$fileData = kFileBase::getFileContent( $realPath);
		if (!$this->isEncrypted()) 
		{
			KalturaLog::info("File of fileSyncId [$this->id] in path $realPath is not encrypted");
			return $fileData;
		}

		$key = $this->getEncryptionKey();
		KalturaLog::debug("Decrypting content of fileSync " . $this->id . ". key is: [$key]");
		$plainData = kEncryptFileUtils::decryptData($fileData, $key, $this->getIv());
		return $plainData;
	}

	public function shouldEncryptFile()
	{

		//check for partner configuration
		if(!$this->getPartnerId() || !PermissionPeer::isValidForPartner(PermissionName::FEATURE_CONTENT_ENCRYPTION, $this->getPartnerId()))
			return false;

		//check fileSync type
		$excludeObjectTypes = array(FileSyncObjectType::BATCHJOB);
		if (in_array($this->object_type, $excludeObjectTypes))
			return false;

		$fileTypeNotToEncrypt = array('log');
		if (in_array($this->getFileExt(), $fileTypeNotToEncrypt))
			return false;

		if ($this->object_type == FileSyncObjectType::ASSET)
		{
			/** @var  Asset $asset */
			$asset = assetPeer::retrieveById($this->object_id);
			if(!$asset)
			{
				KalturaLog::debug("Asset id [" . $this->object_id . "] not found");
				return false;
			}

			$shouldEncrypt = $asset->shouldEncrypt();
			KalturaLog::debug("Asset id [$this->object_id] of type [" . $asset->getType() . "] should be encrypt: [$shouldEncrypt]");
			if (!$shouldEncrypt)
				return false;
		}

		if ($this->getEncryptionKey())
		{
			KalturaLog::info("File of fileSyncId [$this->id] already has key and should not be encrypt again");
			return false;
		}
		return true;
	}
	

	public function setFileSizeFromPath ($filePath)
	{
		$fileSize = kEncryptFileUtils::fileSize($filePath, $this->getEncryptionKey(), $this->getIv());
		$this->setFileSize($fileSize);
	}

	private function getClearTempPath()
	{
		$type = pathinfo($this->getFilePath(), PATHINFO_EXTENSION);
		return sys_get_temp_dir(). "/". $this->getEncryptionKey() . $this->getVersion() . ".$type";
	}
	
	public function getFullPath ()
	{
		return $this->getFileRoot() . $this->getFilePath();
	}

	/**
	 * Its the caller responsibility to remove the file after usage
	 * @return string path to a temporary decrypted file
	 */
	public function createTempClear()
	{
		$realPath = realpath($this->getFullPath());
		$tempPath = $this->getClearTempPath();
		KalturaLog::info("Creating new file for syncId [$this->id] on [$tempPath]");
		if (!file_exists($tempPath))
			kEncryptFileUtils::decryptFile($realPath, $this->getEncryptionKey(), $this->getIv(), $tempPath);
		return $tempPath;
	}

	public function deleteTempClear()
	{
		$tempPath = $this->getClearTempPath();
		if (file_exists($tempPath))
			unlink($tempPath);
	}

	
	public function getFileExt()
	{
		return pathinfo($this->getFullPath(), PATHINFO_EXTENSION);
	}
	
	public function getStatusAsString()
	{
		return (isset($this->statusMap[$this->getStatus()])) ? $this->statusMap[$this->getStatus()] : "Unknown";
	}
	
	public function getExternalUrl($entryId, $format = PlaybackProtocol::HTTP)
	{
		$storage = StorageProfilePeer::retrieveByPK($this->getDc());
		if(!$storage || $storage->getProtocol() === StorageProfile::STORAGE_KALTURA_DC)
			return kDataCenterMgr::getInternalRemoteUrl($this);

		if(is_null($storage->getProtocol()))
		{
			return null;
		}

		$urlManager = DeliveryProfilePeer::getRemoteDeliveryByStorageId(DeliveryProfileDynamicAttributes::init($this->getDc(), $entryId, PlaybackProtocol::HTTP, infraRequestUtils::getProtocol()));
		if(is_null($urlManager) && infraRequestUtils::getProtocol() != 'http')
			$urlManager = DeliveryProfilePeer::getRemoteDeliveryByStorageId(DeliveryProfileDynamicAttributes::init($this->getDc(), $entryId));
		if(is_null($urlManager))
			return null;
		
		$url = $urlManager->getFileSyncUrl($this);
		$baseUrl = $urlManager->getUrl();
		
		$url = ltrim($url, "/");
		if (strpos($url, "://") === false){
			$url = rtrim($baseUrl, "/") . "/".$url ;
		}
		return $url;
	}
	
	/* (non-PHPdoc)
	 * @see BaseFileSync::preUpdate()
	 */
	public function preUpdate(PropelPDO $con = null)
	{
		if($this->isColumnModified(FileSyncPeer::STATUS) 
			&& in_array($this->getStatus(), array(self::FILE_SYNC_STATUS_DELETED, self::FILE_SYNC_STATUS_PURGED)))
		{
			$this->setDeletedId($this->getId());
		}
					
		return parent::preUpdate($con);
	 }

	/* (non-PHPdoc)
	 * @see lib/model/om/BaseFileSync#postUpdate()
	 */
	public function postUpdate(PropelPDO $con = null)
	{
		if ($this->alreadyInSave)
			return parent::postUpdate($con);
		
		$objectDeleted = false;
		if($this->isColumnModified(FileSyncPeer::STATUS) && $this->getStatus() == self::FILE_SYNC_STATUS_DELETED)
			$objectDeleted = true;
			
		$ret = parent::postUpdate($con);
		
		if($objectDeleted)
			kEventsManager::raiseEvent(new kObjectDeletedEvent($this));
			
		return $ret;
	}

	public function getCacheInvalidationKeys()
	{
		return array("fileSync:id=".strtolower($this->getId()), "fileSync:objectId=".strtolower($this->getObjectId()));
	}
	
	/* (non-PHPdoc)
	 * @see BaseFileSync::setStatus()
	 */
	public function setStatus($v)
	{
		if($v == FileSync::FILE_SYNC_STATUS_READY || $v == FileSync::FILE_SYNC_STATUS_ERROR)
			$this->setReadyAt(time());
			
		if ($v == FileSync::FILE_SYNC_STATUS_READY)
		{
			// no longer need these, unset them to reduce table size
			$this->unsetOriginalId();
			$this->unsetOriginalDc();
		}
		
		return parent::setStatus($v);
	}
	
	public function getIsDir() { return $this->getFromCustomData("isDir"); }
	public function setIsDir($v) { $this->putInCustomData("isDir", $v); }
	
	public function getOriginalId() { return $this->getFromCustomData("originalId"); }
	public function setOriginalId($v) { $this->putInCustomData("originalId", $v); }
	public function unsetOriginalId() { $this->removeFromCustomData("originalId"); }

	public function getOriginalDc() { return $this->getFromCustomData("originalDc"); }
	public function setOriginalDc($v) { $this->putInCustomData("originalDc", $v); }
	public function unsetOriginalDc() { $this->removeFromCustomData("originalDc"); }
	
	public function getContentMd5 () { return $this->getFromCustomData("contentMd5"); }
	public function setContentMd5 ($v) { $this->putInCustomData("contentMd5", $v);  }

	public function getEncryptionKey () { return $this->getFromCustomData("encryptionKey"); }
	public function setEncryptionKey ($v) { $this->putInCustomData("encryptionKey", $v);  }
	public function isEncrypted () { return ($this->getFromCustomData("encryptionKey"))? true : false ; }
	public function getIv() {return kConf::get("encryption_iv");}

	public function getSrcPath () { return $this->getFromCustomData("srcPath"); }
	public function setSrcPath ($v) { $this->putInCustomData("srcPath", $v);  }
	public function getSrcEncKey () { return $this->getFromCustomData("srcEncKey"); }
	public function setSrcEncKey ($v) { $this->putInCustomData("srcEncKey", $v);  }

 	/**
	 * Create new fileSync With status pending and new storageId
	 * @param $storageId
	 * @return FileSync
	 * @throws PropelException
	 */
	public function cloneToAnotherStorage($storageId)
	{
		$newfileSync = $this->copy(true);
		$newfileSync->setStatus(FileSync::FILE_SYNC_STATUS_PENDING);
		$newfileSync->setSrcPath($this->getFullPath());
		$newfileSync->setSrcEncKey($this->getSrcEncKey());
		$newfileSync->setFileType(FileSync::FILE_SYNC_FILE_TYPE_URL);
		$newfileSync->setDc($storageId);

		$fileSyncKey = kFileSyncUtils::getKeyForFileSync($newfileSync);
		list($root, $filePath) = kPathManager::getFilePathArr($fileSyncKey, $storageId);
		$newfileSync->setFilePath($filePath);
		return $newfileSync;
	}
}

class FileSyncKey 
{
	public $object_type;
	public $object_id;
	public $version;
	public $object_sub_type;
	public $partner_id;		// this field is not needed for the key, but it is actually derived from the 4 other fields 
	
	/**
	 * 
	 * @return string
	 */
	public function __toString()
	{
		$str = "";
		$str .= "object_type:[{$this->object_type}], ";
		$str .= "object_id:[{$this->object_id}], ";
		$str .= "version:[{$this->version}], ";
		$str .= "object_sub_type[{$this->object_sub_type}], ";
		$str .= "partner_id[{$this->partner_id}]";
		return $str;
	}
	
	public function getObjectType() { return $this->object_type; }
	public function getObjectId() { return $this->object_id; }
	public function getVersion() { return $this->version; }
	public function getObjectSubType() { return $this->object_sub_type; }
	public function getPartnerId() { return $this->partner_id; }
	
	public function setObjectType($v) { $this->object_type = $v; }
	public function setObjectId($v) { $this->object_id = $v; }
	public function setVersion($v) { $this->version = $v; }
	public function setObjectSubType($v) { $this->object_sub_type = $v; }
	public function setPartnerId($v) { $this->partner_id = $v; }
}

class FileSyncException extends Exception
{
	public function __construct ( $type , $sub_type , $allowed_sub_types )
	{
		parent::__construct( "For FileSync type [$type], unknown sub_type [$sub_type]" );
	}
}
