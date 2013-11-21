<?php
/**
 * @package plugins.fileSync
 * @subpackage api.objects
 */
class KalturaFileSync extends KalturaObject implements IFilterable 
{
	/**
	 * 
	 * @var int
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
	 * @filter eq,in
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
	 * @readonly
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
	 * @readonly
	 */
	public $fileRoot;


	
	/**
	 * 
	 * @var string
	 * @readonly
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
	
	public function fromObject ( $source_object  )
	{
		parent::fromObject($source_object);
		
		$this->fileUrl = $source_object->getExternalUrl($this->getEntryId($source_object));
		$this->readyAt = $source_object->getReadyAt(null);
		$this->isCurrentDc = ($source_object->getDc() == kDataCenterMgr::getCurrentDcId());
		
		if($this->fileType == KalturaFileSyncType::LINK)
		{
			$fileSync = kFileSyncUtils::resolve($source_object);
			$this->fileRoot = $fileSync->getFileRoot();
			$this->filePath = $fileSync->getFilePath();
		}
		
		if($this->isCurrentDc)
		{
			$path = $this->fileRoot . $this->filePath;
			$this->fileDiscSize = kFile::fileSize($path);
			$content = file_get_contents($path, false, null, 0, 1024);
			if(ctype_print($content) || ctype_cntrl($content))
				$this->fileContent = $content;
		}
	}
}