<?php
/**
 * @abstract
 */
class KalturaDistributionValidationError extends KalturaObject
{
	/**
	 * @var KalturaDistributionAction
	 */
	public $action;
	
	/**
	 * @var KalturaDistributionErrorType
	 */
	public $errorType;
	
	/**
	 * Missing flavor params id, thumbnail params id or missing metadata field name
	 * @var string
	 */
	public $data;
	
	/**
	 * @var string
	 */
	public $description;
	
	/*
	 * mapping between the field on this object (on the left) and the setter/getter on the object (on the right)  
	 */
	private static $map_between_objects = array 
	 (
		'action',
		'errorType',
		'data',
		'description',
	 );
		 
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
	public function toObject($dbObject = null, $skip = array())
	{
		if (is_null($dbObject))
			return new kDistributionValidationError();
			
		return parent::toObject($dbObject, $skip);
	}
}