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
	
	/**
	 * @var bool
	 */
	public $allowNull;
	
	private static $map_between_objects = array(
		'parentProperty', 
		'filterProperty', 
		'allowNull', 
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
	
	/**
	 * @param KalturaRelatedFilter $filter
	 * @param KalturaObject $parentObject
	 * @return boolean
	 * @throws KalturaAPIException
	 */
	public function apply(KalturaRelatedFilter $filter, KalturaObject $parentObject)
	{
		$filterProperty = $this->filterProperty;
		$parentProperty = $this->parentProperty;
	
		KalturaLog::debug("Mapping " . get_class($parentObject) . "::{$parentProperty}[{$parentObject->$parentProperty}] to " . get_class($filter) . "::$filterProperty");
	
		if(!property_exists($parentObject, $parentProperty))
		{
			throw new KalturaAPIException(KalturaErrors::PROPERTY_IS_NOT_DEFINED, $parentProperty, get_class($parentObject));
		}
		
		if(!property_exists($filter, $filterProperty))
		{
			throw new KalturaAPIException(KalturaErrors::PROPERTY_IS_NOT_DEFINED, $filterProperty, get_class($filter));
		}
		
		if(is_null($parentObject->$parentProperty) && !$this->allowNull)
		{
			KalturaLog::warning("Parent property [" . get_class($parentObject) . "::{$parentProperty}] is null");
			return false;
		}
		
		$filter->$filterProperty = $parentObject->$parentProperty;
		return true;
	}
}