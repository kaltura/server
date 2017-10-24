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

	public function encrypt()
	{
		if (!$this->shouldEncryptFile() || $this->getEncryptionKey())
			return; // in file should not encrypted or he already is encrypted

		$this->setEncryptionKey($this->getObjectId());
		$this->save();

		$key = $this->getKey();

		$realPath = realpath( $this->getFileRoot() . $this->getFilePath() );
		KalturaLog::debug("Encrypting content of fileSync " . $this->id . ". key is: [$key] in path [$realPath]");
		$plainData = file_get_contents( $realPath);
		$cryptData = kEncryptFileUtils::encryptData($plainData, $key);
		file_put_contents( $realPath, $cryptData);
	}

	public function decrypt()
	{
		if (!$this->isEncrypted())
			return null;

		$key = $this->getKey();
		KalturaLog::debug("Decrypting content of fileSync " . $this->id . ". key is: [$key]");
		$realPath = realpath( $this->getFileRoot() . $this->getFilePath() );
		$cryptData = file_get_contents( $realPath);
		$plainData = kEncryptFileUtils::decryptData($cryptData, $key);
		return $plainData;
	}

	public function shouldEncryptFile()
	{
		//check for partner configuration
		if(!$this->getPartnerId() || !PermissionPeer::isValidForPartner(PermissionName::FEATURE_CONTENT_ENCRYPTION, $this->getPartnerId()))
			return false;
		
		$type = pathinfo($this->getFilePath(), PATHINFO_EXTENSION);

		//$fileTypeToEncrypt = kConf::get('image_file_ext');
		//$fileTypeToEncrypt = array_merge($fileTypeToEncrypt, array('pdf','doc'));

		$fileTypeNotToEncrypt = kConf::get('video_file_ext');
		return !in_array($type, $fileTypeNotToEncrypt);

	}


	public function setFileSizeFromPath ($filePath)
	{
		$this->setFileSize(kFile::fileSize($filePath));
	}

	public function getClearTempPath()
	{
		$type = pathinfo($this->getFilePath(), PATHINFO_EXTENSION);
		return sys_get_temp_dir(). "/". $this->getKey() . ".$type";
	}
	
	public function getFullPath ()
	{
		return $this->getFileRoot() . $this->getFilePath();
	}

	public function createTempClear()
	{
		$plainData = $this->decrypt();
		$tempPath = $this->getClearTempPath();
		KalturaLog::info("Creating new file for syncId [$this->id] on [$tempPath]");
		if (!file_exists($tempPath))
			file_put_contents( $tempPath, $plainData);
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
		if(!$storage || $storage->getProtocol() == StorageProfile::STORAGE_KALTURA_DC)
			return kDataCenterMgr::getInternalRemoteUrl($this);

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
	public function getKey () {return $this->getEncryptionKey() .  kConf::get("encryption_salt_key");}

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
