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
	 * @var int
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
	 * @filter eq,in
	 */
	public $status;
	
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
	 * @var KalturaDropFolderFileErrorCode
	 * @filter eq,in
	 */
	public $errorCode;
	
	/**
	 * @var string
	 */
	public $errorDescription;
	
	
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
		'dropFolderId',
		'fileName',
		'fileSize',
		'status',
		'fileSizeLastSetAt',
		'parsedSlug',
		'parsedFlavor',
		'errorCode',
		'errorDescription',
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
			$dbObject = new DropFolderFile();
			
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