<?php
/**
 * @package api
 * @subpackage filters
 */
class KalturaMetadataSearchItem extends KalturaSearchOperator
{
	/**
	 * @var int
	 */
	public $metadataProfileId;
	
	private static $map_between_objects = array
	(
		"metadataProfileId"
	);

	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
	
	public function toObject ( $object_to_fill = null , $props_to_skip = array() )
	{
		KalturaLog::debug("To object: metadataProfileId [$this->metadataProfileId]");
		if(!$object_to_fill)
			$object_to_fill = new MetadataSearchFilter();
			
		return parent::toObject($object_to_fill, $props_to_skip);
	}
}
