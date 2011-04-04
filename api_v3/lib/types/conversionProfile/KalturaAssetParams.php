<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaAssetParams extends KalturaObject implements IFilterable 
{
	/**
	 * The id of the Flavor Params
	 * 
	 * @var int
	 * @readonly
	 */
	public $id;
	
	/**
	 * @var int
	 * @readonly
	 */
	public $partnerId;
	
	/**
	 * The name of the Flavor Params
	 * 
	 * @var string
	 */
	public $name;
	
	/**
	 * System name of the Flavor Params
	 * 
	 * @var string 
	 * @filter eq,in
	 */
	public $systemName;
	
	/**
	 * The description of the Flavor Params
	 * 
	 * @var string
	 */
	public $description;

	/**
	 * Creation date as Unix timestamp (In seconds)
	 *  
	 * @var int
	 * @readonly
	 */
	public $createdAt;
	
	/**
	 * True if those Flavor Params are part of system defaults
	 * 
	 * @var KalturaNullableBoolean
	 * @readonly
	 * @filter eq
	 */
	public $isSystemDefault;
	
	/**
	 * The Flavor Params tags are used to identify the flavor for different usage (e.g. web, hd, mobile)
	 * 
	 * @var string
	 * @filter eq
	 */
	public $tags;

	/**
	 * The container format of the Flavor Params
	 *  
	 * @var KalturaContainerFormat
	 * @filter eq
	 */
	public $format;

	/**
	 * The ingestion origin of the Flavor Params
	 *  
	 * @var KalturaAssetParamsOrigin
	 * @filter eq,in
	 */
	public $origin;

	/**
	 * Array of partner permisison names that required for using this asset params
	 *  
	 * @var KalturaStringArray
	 */
	public $requiredPermissions;
	
	private static $map_between_objects = array
	(
		"id",
		"partnerId",
		"name",
		"systemName",
		"description",
		"createdAt",
		"isSystemDefault" => "isDefault",
		"tags",
		"format",
		"origin",
	);
	
	/* (non-PHPdoc)
	 * @see KalturaObject::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::toObject()
	 */
	public function toObject ( $object_to_fill = null , $props_to_skip = array() )
	{
		if(is_null($object_to_fill))
			$object_to_fill = new assetParams();
			
		$object_to_fill = parent::toObject($object_to_fill, $props_to_skip);
		
		$requiredPermissions = array();
		if($this->requiredPermissions && count($this->requiredPermissions))
		{
			foreach($this->requiredPermissions as $requiredPermission)
				$requiredPermissions[] = $requiredPermission->value;
		}
			
		$object_to_fill->setRequiredPermissions($requiredPermissions);
		
		return $object_to_fill;
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::fromObject()
	 */
	public function fromObject ( $source_object  )
	{
		$this->requiredPermissions = KalturaStringArray::fromStringArray($source_object->getRequiredPermissions());
		return parent::fromObject($source_object);
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
}
