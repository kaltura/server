<?php
/**
 * @package plugins.like
 * @subpackage api.objects
 */
class KalturaLike extends KalturaObject implements IRelatedFilterable
{
	/**
	 * The id of the entry that the like belongs to
	 * @var string
	 * @filter eq
	 */
	public $entryId;
	
	/**
	 * The id of user that the like belongs to
	 * @var string
	 * @filter eq
	 */
	public $userId;
	
	private static $map_between_objects = array
	(
		"entryId",
		"userId" => "puserId"		
	);
	
	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
	
	public function toObject($object_to_fill = null, $props_to_skip = array())
	{
		if (!$object_to_fill)
			$object_to_fill = new KalturaLike();
	
		return parent::toObject($object_to_fill, $props_to_skip);
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
	
