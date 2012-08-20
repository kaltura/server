<?php
/**
 * @package plugins.metadata
 * @subpackage api.filters
 */
class KalturaMetadataSearchItem extends KalturaSearchOperator
{
	/**
	 * @var int
	 */
	public $metadataProfileId;
	
	/**
	 * @var string
	 */
	public $orderBy;
	
	private static $map_between_objects = array
	(
		"metadataProfileId",
		"orderBy"
	);

	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
	
	public function toObject ( $object_to_fill = null , $props_to_skip = array() )
	{
		if(!$object_to_fill)
			$object_to_fill = new MetadataSearchFilter();
			
		return parent::toObject($object_to_fill, $props_to_skip);
	}
}
