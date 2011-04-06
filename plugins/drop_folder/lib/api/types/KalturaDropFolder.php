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
	 * @var string
	 * @filter like
	 */
	public $slugField;
	
	/**
	 * @var string
	 * @filter like
	 */
	public $slugRegex;
	
	/**
	 * @var int
	 */
	public $fileSizeCheckInterval;
	
	/**
	 * @var KalturaDropFolderUnmatchedFilesPolicy
	 */
	public $unmatchedFilePolicy;

	/**
	 * @var KalturaDropFolderFileDeletePolicy
	 */
	public $fileDeletePolicy;
	
	/**
	 * @var int
	 */
	public $autoFileDeleteDays;
	
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
	
	
	
	//TODO: fileHandlersConfig - should have a defined class ?
	//$fileHandlersConfig	

	
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
		'slugField',
		'slugRegex',
		'fileSizeCheckInterval',
		'unmatchedFilePolicy',
		'fileDeletePolicy',
		'autoFileDeleteDays',
		'createdAt',
		'updatedAt',
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
		
		return $dbObject;
	}
	
	public function fromObject ($source_object)
	{
		parent::fromObject($source_object);
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