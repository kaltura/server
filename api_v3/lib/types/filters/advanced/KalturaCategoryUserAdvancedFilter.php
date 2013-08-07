<?php
/**
 * @package api
 * @subpackage filters
 */
class KalturaCategoryUserAdvancedFilter extends KalturaSearchItem
{
	/**
	 * @var string
	 */
	public $memberIdEq;
	
	/**
	 * @var string
	 */
	public $memberIdIn;
	
	/**
	 * @var string
	 */
	public $memberPermissionsMultiLikeOr;
	
	/**
	 * @var string
	 */
	public $memberPermissionsMultiLikeAnd;
	
	private static $map_between_objects = array
	(
		"memberIdEq",
		"memberIdIn",
		"memberPermissionsMultiLikeOr",
		"memberPermissionsMultiLikeAnd",
	);

	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
	
	public function toObject ( $object_to_fill = null , $props_to_skip = array() )
	{
		if(!$object_to_fill)
			$object_to_fill = new kCategoryKuserAdvancedFilter();
		
		if (!$this->memberIdEq && !$this->memberIdIn)
		{
			throw new KalturaAPIException(KalturaErrors::PROPERTY_VALIDATION_CANNOT_BE_NULL, 'memberIdEq,memberIdIn');
		}
		
		if (!$this->memberPermissionsMultiLikeAnd && !$this->memberPermissionsMultiLikeOr)
		{
			throw new KalturaAPIException(KalturaErrors::PROPERTY_VALIDATION_CANNOT_BE_NULL, 'memberIdEq,memberIdIn');
		}
			
		return parent::toObject($object_to_fill, $props_to_skip);
	}
}