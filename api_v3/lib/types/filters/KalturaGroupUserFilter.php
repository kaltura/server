<?php
/**
 * @package api
 * @subpackage filters
 */
class KalturaGroupUserFilter extends KalturaGroupUserBaseFilter
{

	static private $map_between_objects = array	();

	/* (non-PHPdoc)
	 * @see KalturaObject::toObject()
	 */
	public function toObject($coreFilter = null, $props_to_skip = array()) 
	{
		if(is_null($coreFilter))
			$coreFilter = new KuserKgroupFilter();
			
		return parent::toObject($coreFilter, $props_to_skip);
	}
	
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
}
