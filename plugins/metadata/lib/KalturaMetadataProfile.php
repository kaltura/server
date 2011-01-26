<?php
/**
 * @package plugins.metadata
 * @subpackage api.objects
 */
class KalturaMetadataProfile extends KalturaObject implements IFilterable 
{
	/**
	 * 
	 * @var int
	 * @filter eq
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
	 * @var KalturaMetadataObjectType
	 * @filter eq
	 */
	public $metadataObjectType;


	
	/**
	 * 
	 * @var int
	 * @filter eq
	 * @readonly
	 */
	public $version;


	
	/**
	 * 
	 * @var string
	 */
	public $name;


	
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
	 * @var KalturaMetadataProfileStatus
	 * @filter eq,in
	 * @readonly
	 */
	public $status;


	
	/**
	 * 
	 * @var string
	 * @readonly
	 */
	public $xsd;


	
	/**
	 * 
	 * @var string
	 * @readonly
	 */
	public $views;
	
	private static $map_between_objects = array
	(
		"id",
		"partnerId",
		"metadataObjectType" => "objectType",
		"version",
		"name",
		"createdAt",
		"updatedAt",
		"status",
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
	
	public function toObject($dbMetadataProfile = null, $propsToSkip = array())
	{
		if(is_null($dbMetadataProfile))
			$dbMetadataProfile = new MetadataProfile();
			
		return parent::toObject($dbMetadataProfile, $propsToSkip);
	}
	
	public function fromObject($source_object)
	{
		parent::fromObject($source_object);

		$key = $source_object->getSyncKey(MetadataProfile::FILE_SYNC_METADATA_DEFINITION);
		$this->xsd = kFileSyncUtils::file_get_contents($key, true, false);
		
		$key = $source_object->getSyncKey(MetadataProfile::FILE_SYNC_METADATA_VIEWS);
		$this->views = kFileSyncUtils::file_get_contents($key, true, false);
	}
}