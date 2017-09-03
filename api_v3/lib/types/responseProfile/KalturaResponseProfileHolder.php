<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaResponseProfileHolder extends KalturaBaseResponseProfile
{
	/**
	 * Auto generated numeric identifier
	 * 
	 * @var bigint
	 */
	public $id;
	
	/**
	 * Unique system name
	 * 
	 * @var string
	 */
	public $systemName;
	
	private static $map_between_objects = array(
		'id', 
		'systemName', 
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
		if($this->isNull('id') && $this->isNull('systemName'))
    		throw new KalturaAPIException(KalturaErrors::PROPERTY_VALIDATION_CANNOT_BE_NULL, $this->getFormattedPropertyNameWithClassName('id') . ' and ' . $this->getFormattedPropertyNameWithClassName('systemName'));
    		
		parent::validateForUsage($sourceObject, $propertiesToSkip);
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::toObject
	 */
	public function toObject($object = null, $propertiesToSkip = array())
	{
		if(is_null($object))
		{
			$object = new kResponseProfileHolder();
		}
		
		return parent::toObject($object, $propertiesToSkip);
	}
}