<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaResponseProfileMapping extends KalturaObject
{
	/**
	 * @var string
	 */
	public $parentProperty;
	
	/**
	 * @var string
	 */
	public $filterProperty;
	
	private static $map_between_objects = array(
		'parentProperty', 
		'filterProperty', 
	);
	
	/* (non-PHPdoc)
	 * @see KalturaObject::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::validateForUsage($sourceObject, $propertiesToSkip)
	 */
	public function validateForUsage($sourceObject, $propertiesToSkip = array())
	{
		$this->validatePropertyNotNull(array(
			'parentProperty', 
			'filterProperty', 
		));
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::toObject($object_to_fill, $props_to_skip)
	 */
	public function toObject($object = null, $propertiesToSkip = array())
	{
		if(is_null($object))
		{
			$object = new kResponseProfileMapping();
		}
		
		return parent::toObject($object, $propertiesToSkip);
	}
	
	public function apply(KalturaRelatedFilter $filter, KalturaObject $parentObject)
	{
		$filterProperty = $this->filterProperty . 'Equal';
		$parentProperty = $this->parentProperty;
	
		KalturaLog::debug("Mapping " . get_class($parentObject) . "::{$parentProperty}[{$parentObject->$parentProperty}] to " . get_class($filter) . "::$filterProperty");
		
		if(is_null($parentObject->$parentProperty))
		{
			throw new KalturaAPIException(KalturaErrors::PROPERTY_VALIDATION_CANNOT_BE_NULL, get_class($parentObject) . "::$parentProperty");
		}
		
		if(!property_exists($filter, $filterProperty))
		{
			throw new KalturaAPIException(KalturaErrors::PROPERTY_IS_NOT_DEFINED, $filterProperty, get_class($filter));
		}
		
		$filter->$filterProperty = $parentObject->$parentProperty;
	}
}