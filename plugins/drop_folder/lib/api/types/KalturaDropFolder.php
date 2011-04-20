<?php
/**
 * @package plugins.dropFolder
 * @subpackage api.objects
 */
class KalturaDropFolder extends KalturaObject implements IFilterable
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
	 * @var string
	 * @filter like,order
	 */
	public $name;
	
	/**
	 * @var string
	 */
	public $description;
	
	/**
	 * @var KalturaDropFolderType
	 * @filter eq,in
	 */
	public $type;
	
	/**
	 * @var KalturaDropFolderStatus
	 * @filter eq,in
	 */
	public $status;
	
	/**
	 * @var int
	 * @filter eq,in
	 */
	public $ingestionProfileId;
	
	/**
	 * @var int
	 * @filter eq,in
	 */
	public $dc;
	
	/**
	 * @var string
	 * @filter like
	 */
	public $path;
	
	/**
	 * @var int
	 */
	public $fileSizeCheckInterval;
	
	/**
	 * @var KalturaDropFolderFileDeletePolicy
	 */
	public $fileDeletePolicy;
	
	/**
	 * @var int
	 */
	public $autoFileDeleteDays;
	
	
	/**
	 * @var KalturaDropFolderFileHandlerType
	 * @filter eq,in
	 */
	public $fileHandlerType;
	
	/**
	 * @var string
	 * @filter like,mlikeor,mlikeand
	 */
	public $fileNamePatterns;
	
	/**
	 * @var KalturaDropFolderFileHandlerConfig
	 */
	public $fileHandlerConfig;

	/**
	 * @var string
	 * @filter like,mlikeor,mlikeand
	 */
	public $tags;
	
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

	
	/*
	 * mapping between the field on this object (on the left) and the setter/getter on the entry object (on the right)  
	 */
	private static $map_between_objects = array(
		'id',
		'partnerId',
		'name',
		'description',
		'type',
		'status',
		'ingestionProfileId' => 'conversionProfileId',
		'dc',
		'path',
		'fileSizeCheckInterval',
		'fileDeletePolicy',
		'autoFileDeleteDays',
		'fileHandlerType',
		'fileNamePatterns',
		'createdAt',
		'updatedAt',
		'tags',
	 );
		 
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
	public function toObject($dbObject = null, $skip = array())
	{
		if (is_null($dbObject))
			$dbObject = new DropFolder();
			
		parent::toObject($dbObject, $skip);
		if ($this->fileHandlerConfig)
		{
			$dbFileHandlerConfig = $this->fileHandlerConfig->toObject();
			$dbObject->setFileHandlerConfig($dbFileHandlerConfig);
		}
		
		return $dbObject;
	}
	
	public function fromObject ($source_object)
	{
		parent::fromObject($source_object);
		$dbFileHandlerConfig = $source_object->getFileHandlerConfig();
		if ($dbFileHandlerConfig)
		{
			$apiFileHandlerConfig = KalturaPluginManager::loadObject('KalturaDropFolderFileHandlerConfig', $dbFileHandlerConfig->getHandlerType());
			$apiFileHandlerConfig->fromObject($dbFileHandlerConfig);
			$this->fileHandlerConfig  = $apiFileHandlerConfig;
		}
	}

	
	public function getExtraFilters()
	{
		return array();
	}
	
	public function getFilterDocs()
	{
		return array();
	}
	
}