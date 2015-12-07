<?php
/**
 * @package api
 * @subpackage filters
 */
class KalturaSearchComparableCondition extends KalturaSearchCondition
{
	/**
	 * @var KalturaSearchConditionComparison
	 */
	public $comparison;
	
	private static $map_between_objects = array
	(
		"comparison",
	);

	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
	
	public function toObject ( $object_to_fill = null , $props_to_skip = array() )
	{
		KalturaLog::debug("To object: field [$this->field] value [$this->value]");
		
		if(is_null($object_to_fill))
			$object_to_fill = new AdvancedSearchFilterComparableCondition();
			
		return parent::toObject($object_to_fill, $props_to_skip);		
	}
}
