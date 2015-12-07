<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaPermission extends KalturaObject implements IRelatedFilterable
{
	/**
	 * @var int
	 * @readonly
	 * @filter eq,in,order
	 */
	public $id;
	
	
	/**
	 * @var KalturaPermissionType
	 * @filter eq,in
	 * @readonly
	 */
	public $type;
	
	
	/**
	 * @var string
	 * @filter eq,in,order
	 */
	public $name;
	
	
	/**
	 * @var string
	 * @filter like
	 */
	public $friendlyName;

	
	/**
	 * @var string
	 * @filter like
	 */
	public $description;
	

	/**
	 * @var KalturaPermissionStatus
	 * @filter eq,in
	 */
	public $status;
	
	
	/**
	 * @var int
	 * @readonly
	 * @filter eq,in
	 */
	public $partnerId;
	
	
	/**
	 * @var string
	 * @filter mlikeor, mlikeand
	 */
	public $dependsOnPermissionNames;
	
	
	/**
	 * @var string
	 * @filter mlikeor, mlikeand
	 */
	public $tags;
	
	
	/**
	 * @var string
	 */
	public $permissionItemsIds;
	
	
	/**
	 * @var time
	 * @readonly
	 * @filter gte,lte,order
	 */
	public $createdAt;

	
	/**
	 * @var time
	 * @readonly
	 * @filter gte,lte,order
	 */
	public $updatedAt;
	
	/**
	 * @var string
	 */
	public $partnerGroup;
	
	
	/*
	 * mapping between the field on this object (on the left) and the setter/getter on the entry object (on the right)  
	 */
	private static $map_between_objects = array(
		'id',
		'name',
		'friendlyName',
		'description',
		'status',
		'partnerId',
		'tags',
		'createdAt',
		'updatedAt',
		'dependsOnPermissionNames',
		'partnerGroup',
		'type',
	 );
		 
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
	public function toObject($dbObject = null, $skip = array())
	{
		if (is_null($dbObject))
			$dbObject = new Permission();
			
		parent::toObject($dbObject, $skip);
		
		// copy permission items IDs
		$dbObject->setPermissionItems($this->permissionItemsIds);
					
		return $dbObject;
	}
	
	public function doFromObject($source_object, KalturaDetachedResponseProfile $responseProfile = null)
	{
		parent::doFromObject($source_object, $responseProfile);
		
		// copy permission items IDs
		if($this->shouldGet('permissionItemsIds', $responseProfile))
		{
			$itemIdsArray = $source_object->getPermissionItemIds();
			if ($itemIdsArray && count($itemIdsArray) > 0) {
				$this->permissionItemsIds = implode(',', $itemIdsArray);
			}
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