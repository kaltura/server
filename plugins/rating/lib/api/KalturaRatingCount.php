<?php
/**
 * @package plugins.rating
 * @subpackage api.objects
 * @relatedService RatingService
 */

class KalturaRatingCount extends KalturaObject implements IRelatedFilterable
{
	/**
	 * @var string
	 * @filter eq
	 */
	public $entryId;
	
	/**
	 * @var int
	 * @filter in
	 */
	public $rank;
	
	/**
	 * @var int
	 */
	public $count;
	
	private static $map_between_objects = array
	(
		"entryId",
		"rank",
		"count"
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