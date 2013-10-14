<?php
/**
 * @package plugins.dropFolder
 * @subpackage api.objects
 */
class KalturaDropFolderFile extends KalturaObject implements IFilterable
{
	/**
	 * @var int
	 * @readonly
	 * @filter eq,in,order
	 */
	public $id;
	
	/**
	 * @var int
	 * @readonly
	 * @filter eq,in
	 */
	public $partnerId;
	
	/**
	 * @var int
	 * @insertonly
	 * @filter eq,in
	 */
	public $dropFolderId;
	
	/**
	 * @var string
	 * @insertonly
	 * @filter eq,in,like,order
	 */
	public $fileName;
	
	/**
	 * @var float
	 * @filter order
	 */
	public $fileSize;
	
	/**
	 * @var int
	 * @filter order
	 * @readonly
	 */
	public $fileSizeLastSetAt;
	
	/**
	 * @var KalturaDropFolderFileStatus
	 * @filter eq,in,notin
	 * @readonly
	 */
	public $status;
	
	/**
	 * @var KalturaDropFolderType
	 * @readonly
	 */
	public $type;
	
	/**
	 * @var string
	 * @filter eq,in,like,order
	 */
	public $parsedSlug;
	
	/**
	 * @var string
	 * @filter eq,in,like,order
	 */
	public $parsedFlavor;
	
	/**
	 * @var int
	 * @filter eq
	 */
	public $leadDropFolderFileId;
	
	/**
	 * @var int
	 * @filter eq
	 */
	public $deletedDropFolderFileId;
	
	/**
	 * @var string
	 * @filter eq
	 */
	public $entryId;
	
	
	/**
	 * @var KalturaDropFolderFileErrorCode
	 * @filter eq,in
	 */
	public $errorCode;
	
	/**
	 * @var string
	 */
	public $errorDescription;
	
	/**
	 * @var string
	 */
	public $lastModificationTime;
	
	
	/**
	 * @var int
	 * @readonly
	 * @filter gte,lte,order
	 */
	public $createdAt;

	/**
	 * @var int
	 * @readonly
	 * @filter gte,lte,order
	 */
	public $updatedAt;

	/**
	 * @var int
	 */
	public $uploadStartDetectedAt;
	
	/**
	 * @var int
	 */
	public $uploadEndDetectedAt;
	
	/**
	 * @var int
	 */
	public $importStartedAt;
	
	/**
	 * @var int
	 */
	public $importEndedAt;

	/**
	 * @var int
	 * @readonly
	 */
	public $batchJobId;

	
	/*
	 * mapping between the field on this object (on the left) and the setter/getter on the entry object (on the right)  
	 */
	private static $map_between_objects = array(
		'id',
		'partnerId',
		'dropFolderId',
		'fileName',
		'fileSize',
		'status',
		'type',
		'fileSizeLastSetAt',
		'parsedSlug',
		'parsedFlavor',
		'leadDropFolderFileId',
		'deletedDropFolderFileId',
		'entryId',
		'errorCode',
		'errorDescription',
	    'lastModificationTime',
		'createdAt',
		'updatedAt',
		'uploadStartDetectedAt',
		'uploadEndDetectedAt',
		'importStartedAt',
		'importEndedAt',
		'batchJobId',
	 );
		 
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

	public function toObject($dbObject = null, $skip = array())
	{
		if (is_null($dbObject))
			$dbObject = new DropFolderFile();
		
			
		parent::toObject($dbObject, $skip);
		KalturaLog::debug('file size in api: '. $this->fileSize);
		KalturaLog::debug("dbObject: " . print_r($dbObject, true));
		return $dbObject;
	}
	
	public function fromObject ($source_object)
	{
		parent::fromObject($source_object);
		
		$this->uploadStartDetectedAt = $source_object->getUploadStartDetectedAt(null);
		$this->uploadEndDetectedAt = $source_object->getUploadEndDetectedAt(null);
		$this->importStartedAt = $source_object->getImportStartedAt(null);
		$this->importEndedAt = $source_object->getImportEndedAt(null);		
	}

	public function getExtraFilters()
	{
		return array();
	}
	
	public function getFilterDocs()
	{
		return array();
	}
	
	/**
	 * @param int $type
	 * @return KalturaDropFolder
	 */
	static function getInstanceByType ($type)
	{
		$ret = null;
		switch ($type) {
			case DropFolderType::FTP:
			case DropFolderType::SFTP:
			case DropFolderType::LOCAL:
			case DropFolderType::SCP:
			case DropFolderType::S3:
				$ret = new KalturaDropFolderFile();				
				break;
			
			default:
				$ret = KalturaPluginManager::loadObject('KalturaDropFolderFile', $type);
				break;
		}
		
		if (!$ret)
			$ret = new KalturaDropFolderFile();
			
		return $ret;
	}
}