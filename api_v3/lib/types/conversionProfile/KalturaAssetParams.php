<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaAssetParams extends KalturaObject implements IRelatedFilterable 
{
	/**
	 * The id of the Flavor Params
	 * 
	 * @var int
	 * @filter eq,in
	 * @readonly
	 */
	public $id;
	
	/**
	 * @var int
	 * @requiresPermission insert,update
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
	 * @var time
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
	 * Array of partner permisison names that required for using this asset params
	 *  
	 * @var KalturaStringArray
	 */
	public $requiredPermissions;

	/**
	 * Id of remote storage profile that used to get the source, zero indicates Kaltura data center
	 *  
	 * @var int
	 */
	public $sourceRemoteStorageProfileId;

	/**
	 * Comma seperated ids of remote storage profiles that the flavor distributed to, the distribution done by the conversion engine
	 *  
	 * @var int
	 */
	public $remoteStorageProfileIds;

	/**
	 * Media parser type to be used for post-conversion validation
	 *  
	 * @var KalturaMediaParserType
	 */
	public $mediaParserType;

	/**
	 * Comma seperated ids of source flavor params this flavor is created from
	 *  
	 * @var string
	 */
	public $sourceAssetParamsIds;
	
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
		"sourceRemoteStorageProfileId",
		"remoteStorageProfileIds",
		"mediaParserType",
		"sourceAssetParamsIds",
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
	public function doFromObject($source_object, KalturaDetachedResponseProfile $responseProfile = null)
	{
		/* @var $source_object assetParams */
		if($this->shouldGet('requiredPermissions', $responseProfile))
			$this->requiredPermissions = KalturaStringArray::fromStringArray($source_object->getRequiredPermissions());
			
		return parent::doFromObject($source_object, $responseProfile);
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
