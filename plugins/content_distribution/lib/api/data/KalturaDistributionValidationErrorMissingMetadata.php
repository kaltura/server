<?php
class KalturaDistributionValidationErrorMissingMetadata extends KalturaDistributionValidationError
{
	/**
	 * @var string
	 */
	public $fieldName;

	/*
	 * mapping between the field on this object (on the left) and the setter/getter on the object (on the right)  
	 */
	private static $map_between_objects = array 
	 (
	 	'fieldName' => 'data',
	 );
		 
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
}