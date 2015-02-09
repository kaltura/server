<?php
/**
 * @package api
 * @subpackage filters
 */
class KalturaCategoryUserFilter extends KalturaCategoryUserBaseFilter
{
	static private $map_between_objects = array
	(
		"freeText" => "_mlikeor_screen_name-puser_id",
		"categoryDirectMembers" => "_category_direct_members",
	);
	
	/* (non-PHPdoc)
	 * @see KalturaObject::toObject()
	 */
	public function toObject($coreFilter = null, $props_to_skip = array()) 
	{
		if(is_null($coreFilter))
			$coreFilter = new categoryKuserFilter();
			
		return parent::toObject($coreFilter, $props_to_skip);
	}
	
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
	
	/**
	 * Return the list of categoryUser that are not inherited from parent category - only the direct categoryUsers.
	 * @var bool
	 * @requiresPermission read
	 */
	public $categoryDirectMembers;
	
	/**
	 * Free text search on user id or screen name
	 * @var string
	 */
	public $freeText;

	/**
	 * Return a list of categoryUser that related to the userId in this field by groups
	 * @var string
	 */
	public $relatedGroupsByUserId;

}
