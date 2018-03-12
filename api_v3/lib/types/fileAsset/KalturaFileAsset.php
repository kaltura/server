<?php
/**
 * @package api
 * @subpackage api.objects
 */
class KalturaFileAsset extends KalturaObject implements IRelatedFilterable 
{
	/**
	 * @var bigint
	 * @filter eq,in
	 * @readonly
	 */
	public $id;

	
	/**
	 * @var int
	 * @filter eq
	 * @readonly
	 */
	public $partnerId;

	
	/**
	 * 
	 * @var KalturaFileAssetObjectType
	 * @filter eq
	 * @insertonly
	 */
	public $fileAssetObjectType;

	
	/**
	 * 
	 * @var string
	 * @filter eq,in
	 * @insertonly
	 */
	public $objectId;

	
	/**
	 * 
	 * @var string
	 */
	public $name;

	
	/**
	 * 
	 * @var string
	 */
	public $systemName;

	
	/**
	 * 
	 * @var string
	 */
	public $fileExt;

	
	/**
	 * 
	 * @var int
	 * @readonly
	 */
	public $version;

	
	/**
	 * 
	 * @var int
	 * @filter gte,lte,order
	 * @readonly
	 */
	public $createdAt;


	/**
	 * 
	 * @var int
	 * @filter gte,lte,order
	 * @readonly
	 */
	public $updatedAt;

	
	/**
	 * 
	 * @var KalturaFileAssetStatus
	 * @filter eq,in
	 * @readonly
	 */
	public $status;
	
	private static $map_between_objects = array
	(
		"id",
		"partnerId",
		"fileAssetObjectType" => "objectType",
		"objectId",
		"name",
		"systemName",
		"fileExt",
		"version",
		"createdAt",
		"updatedAt",
		"status",
	);
	
	/* (non-PHPdoc)
	 * @see KalturaObject::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
	/* (non-PHPdoc)
	 * @see IFilterable::getExtraFilters()
	 */
	public function getExtraFilters()
	{
		return array();
	}
	
	/* (non-PHPdoc)
	 * @see IFilterable::getFilterDocs()
	 */
	public function getFilterDocs()
	{
		return array();
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::toObject($object_to_fill, $props_to_skip)
	 */
	public function toObject($dbFileAsset = null, $propsToSkip = array())
	{
		if(is_null($dbFileAsset))
			$dbFileAsset = new FileAsset();
			
		return parent::toObject($dbFileAsset, $propsToSkip);
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::validateForInsert($propertiesToSkip)
	 */
	public function validateForInsert($propertiesToSkip = array())
	{
		$this->validatePropertyNotNull('fileAssetObjectType');
		$this->validatePropertyNotNull('objectId');

		$peerType=null;

		switch($this->fileAssetObjectType)
		{
			case KalturaFileAssetObjectType::UI_CONF:
				$peerType = uiConfPeer;
				break;
			case KalturaFileAssetObjectType::ENTRY:
				$peerType = entryPeer;
		}
		if($peerType) {
			$object = $peerType::retrieveByPK($this->objectId);
			if (!$object)
				throw new KalturaAPIException(APIErrors::INVALID_UI_CONF_ID, $this->objectId);
		}
	}
}