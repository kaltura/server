<?php
/**
 * @package api
 * @subpackage filters
 */
class KalturaCategoryUserFilter extends KalturaCategoryUserBaseFilter
{
	private $map_between_objects = array
	(
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
		return array_merge(parent::getMapBetweenObjects(), $this->map_between_objects);
	}
	
	
	/**
	 * Return the list of categoryUser that are not inherited from parent category - only the direct categoryUsers.
	 * @var bool
	 * @requiresPermission read
	 */
	public $categoryDirectMembers;
}
