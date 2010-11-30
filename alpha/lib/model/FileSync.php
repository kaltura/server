<?php

/**
 * Subclass for representing a row from the 'file_sync' table.
 *
 * 
 *
 * @package lib.model
 */ 
class FileSync extends BaseFileSync
{
	const FILE_SYNC_FILE_TYPE_FILE = 1;
	const FILE_SYNC_FILE_TYPE_LINK = 2;
	const FILE_SYNC_FILE_TYPE_URL = 3;
	
	const FILE_SYNC_OBJECT_TYPE_ENTRY = 1;
	const FILE_SYNC_OBJECT_TYPE_UICONF = 2;
	const FILE_SYNC_OBJECT_TYPE_BATCHJOB = 3;
	const FILE_SYNC_OBJECT_TYPE_FLAVOR_ASSET = 4;
	const FILE_SYNC_OBJECT_TYPE_METADATA = 5;
	const FILE_SYNC_OBJECT_TYPE_METADATA_PROFILE = 6;

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
	
	public function getFullPath ()
	{
		return $this->getFileRoot() . $this->getFilePath();
	}
	
	public function getFileExt()
	{
		return pathinfo($this->getFullPath(), PATHINFO_EXTENSION);
	}
	
	public function getStatusAsString()
	{
		return (isset($this->statusMap[$this->getStatus()])) ? $this->statusMap[$this->getStatus()] : "Unknown";
	}
	
	public function getExternalUrl($format = StorageProfile::PLAY_FORMAT_HTTP)
	{
		$storage = StorageProfilePeer::retrieveByPK($this->getDc());
		if(!$storage || $storage->getProtocol() == StorageProfile::STORAGE_KALTURA_DC)
			return kDataCenterMgr::getInternalRemoteUrl($this);
			
		$urlManager = kUrlManager::getUrlManagerByStorageProfile($this->getDc());
		$url = $urlManager->getFileSyncUrl($this);
		
		if ($format == StorageProfile::PLAY_FORMAT_RTMP)
			return $storage->getDeliveryRmpBaseUrl() . '/' . $url;
			
		return $storage->getDeliveryHttpBaseUrl() . '/' . $url;
	}
	
	public function getSmoothStreamUrl()
	{
		$storage = StorageProfilePeer::retrieveByPK($this->getDc());
		return $storage->getDeliveryIisBaseUrl() . '/' . $this->getFilePath();
	}

	/* (non-PHPdoc)
	 * @see lib/model/om/BaseFileSync#postUpdate()
	 */
	public function postUpdate(PropelPDO $con = null)
	{
		$objectDeleted = false;
		if($this->isColumnModified(FileSyncPeer::STATUS) && $this->getStatus() == self::FILE_SYNC_STATUS_DELETED)
			$objectDeleted = true;
			
		$ret = parent::postUpdate($con);
		
		if($objectDeleted)
			kEventsManager::raiseEvent(new kObjectDeletedEvent($this));
			
		return $ret;
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

interface ISyncableFile 
{
	public function getSyncKey( $sub_type , $version=null);
	
	/**
	 * will return a pair of file_root and file_path
	 *
	 * @param int $sub_type
	 * @param unknown_type $version
	 */
	public function generateFilePathArr ( $sub_type , $version=null ); 

	/**
	 * will return a string of the base file name
	 *
	 * @param int $sub_type
	 * @param unknown_type $version
	 */	
	public function generateFileName( $sub_type, $version = null);

	/**
	 * @return FileSync
	 */
	public function getFileSync ( );
	
	/**
	 * @param FileSync $file_sync
	 */
	public function setFileSync ( FileSync $file_sync );
	
	/**
	 * @return int
	 */
	public function getPartnerId();
}

class FileSyncException extends Exception
{
	public function FileSyncException ( $type , $sub_type , $allowed_sub_types )
	{
		parent::__construct( "For FileSync type [$type], unknown sub_type [$sub_type]" );
	}
}