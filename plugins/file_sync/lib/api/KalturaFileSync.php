<?php
/**
 * @package plugins.fileSync
 * @subpackage api.objects
 */
class KalturaFileSync extends KalturaObject implements IFilterable 
{
	/**
	 * 
	 * @var bigint
	 * @readonly
	 */
	public $id;

	
	/**
	 * 
	 * @var int
	 * @filter eq
	 * @readonly
	 */
	public $partnerId;


	
	/**
	 * 
	 * @var KalturaFileSyncObjectType
	 * @filter eq,in
	 * @readonly
	 */
	public $fileObjectType;


	
	/**
	 * 
	 * @var string
	 * @filter eq,in
	 * @readonly
	 */
	public $objectId;


	
	/**
	 * 
	 * @var string
	 * @filter eq,in,order
	 * @readonly
	 */
	public $version;


	
	/**
	 * 
	 * @var int
	 * @filter eq,in
	 * @readonly
	 */
	public $objectSubType;


	
	/**
	 * 
	 * @var string
	 * @filter eq,in
	 * @readonly
	 */
	public $dc;


	
	/**
	 * 
	 * @var int
	 * @filter eq
	 * @readonly
	 */
	public $original;


	
	/**
	 * 
	 * @var time
	 * @filter gte,lte,order
	 * @readonly
	 */
	public $createdAt;


	
	/**
	 * 
	 * @var time
	 * @filter gte,lte,order
	 * @readonly
	 */
	public $updatedAt;


	
	/**
	 * 
	 * @var int
	 * @filter gte,lte,order
	 * @readonly
	 */
	public $readyAt;


	
	/**
	 * 
	 * @var int
	 * @filter gte,lte,order
	 * @readonly
	 */
	public $syncTime;


	
	/**
	 * 
	 * @var KalturaFileSyncStatus
	 * @filter eq,in
	 */
	public $status;


	
	/**
	 * 
	 * @var KalturaFileSyncType
	 * @filter eq,in
	 * @readonly
	 */
	public $fileType;


	
	/**
	 * 
	 * @var int
	 * @filter eq
	 * @readonly
	 */
	public $linkedId;


	
	/**
	 * 
	 * @var int
	 * @filter gte,lte
	 * @readonly
	 */
	public $linkCount;


	
	/**
	 * 
	 * @var string
	 */
	public $fileRoot;


	
	/**
	 * 
	 * @var string
	 */
	public $filePath;


	
	/**
	 * 
	 * @var float
	 * @filter gte,lte,order
	 * @readonly
	 */
	public $fileSize;


	
	/**
	 * 
	 * @var string
	 * @readonly
	 */
	public $fileUrl;


	
	/**
	 * 
	 * @var string
	 * @readonly
	 */
	public $fileContent;


	
	/**
	 * 
	 * @var float
	 * @readonly
	 */
	public $fileDiscSize;


	
	/**
	 * 
	 * @var bool
	 * @readonly
	 */
	public $isCurrentDc;
	
	/**
	 *
	 * @var bool
	 * @readonly
	 */
	public $isDir;

	/**
	 *
	 * @var int
	 * @readonly
	 */
	public $originalId;
	
	private static $map_between_objects = array
	(
		"id",
		"partnerId",
		"fileObjectType" =>  "objectType",
		"objectId",
		"version",
		"objectSubType",
		"dc",
		"original",
		"createdAt",
		"updatedAt",
		"syncTime",
		"status",
		"fileType",
		"linkedId",
		"linkCount",
		"fileRoot",
		"filePath",
		"fileSize",
		"readyAt",
		"isDir",
		"originalId",
	);
	
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
	public function getExtraFilters()
	{
		return array();
	}
	
	public function getFilterDocs()
	{
		return array();
	}
	
	public function getEntryId(FileSync $fileSync)
	{
		if($fileSync->getObjectType() == FileSyncObjectType::ENTRY)
			return $fileSync->getObjectId();
			
		if($fileSync->getObjectType() == FileSyncObjectType::BATCHJOB)
		{
			$job = BatchJobPeer::retrieveByPK($fileSync->getObjectId());
			if($job)
				return $job->getEntryId();
		}
			
		if($fileSync->getObjectType() == FileSyncObjectType::FLAVOR_ASSET)
		{
			$flavor = assetPeer::retrieveById($fileSync->getObjectId());
			if($flavor)
				return $flavor->getEntryId();
		}
			
		return null;
	}
	
	public function toObject($dbFileSync = null, $propsToSkip = array())
	{
		if(is_null($dbFileSync))
			$dbFileSync = new FileSync();
			
		return parent::toObject($dbFileSync, $propsToSkip);
	}
	
	public function doFromObject($source_object, KalturaDetachedResponseProfile $responseProfile = null)
	{
		parent::doFromObject($source_object, $responseProfile);
		
		if($this->shouldGet('fileUrl', $responseProfile))
			$this->fileUrl = $source_object->getExternalUrl($this->getEntryId($source_object));
		
		if($this->shouldGet('isCurrentDc', $responseProfile))
			$this->isCurrentDc = ($source_object->getDc() == kDataCenterMgr::getCurrentDcId());
		
		if($source_object->getFileType() == FileSync::FILE_SYNC_FILE_TYPE_LINK && 
			($this->shouldGet('fileRoot', $responseProfile) || $this->shouldGet('filePath', $responseProfile)))
		{
			$fileSync = kFileSyncUtils::resolve($source_object);
			$this->fileRoot = $fileSync->getFileRoot();
			$this->filePath = $fileSync->getFilePath();
		}
		
		if($source_object->getDc() == kDataCenterMgr::getCurrentDcId())
		{
			$path = $source_object->getFullPath();
			if($this->shouldGet('fileDiscSize', $responseProfile))
				$this->fileDiscSize = kFile::fileSize($path);
			if($this->shouldGet('fileContent', $responseProfile))
			{
				$content = substr(kFileSyncUtils::getContentsByFileSync($source_object), 0, 1024);
				if(ctype_print($content) || ctype_cntrl($content))
					$this->fileContent = $content;
			}
		}
	}
}
