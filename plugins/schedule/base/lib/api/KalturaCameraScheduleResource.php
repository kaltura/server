<?php
/**
 * @package plugins.schedule
 * @subpackage api.objects
 */
class KalturaCameraScheduleResource extends KalturaScheduleResource
{
	/**
	 * URL of the stream
	 * @var string
	 * @minLength 1
	 * @maxLength 256
	 */
	public $streamUrl;
	
	/*
	 * Mapping between the field on this object (on the left) and the setter/getter on the entry object (on the right)  
	 */
	private static $map_between_objects = array 
	(	
		'streamUrl',
	);
		 
	/* (non-PHPdoc)
	 * @see KalturaObject::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
		 
	/* (non-PHPdoc)
	 * @see KalturaObject::validateForInsert($propertiesToSkip)
	 */
	public function validateForInsert($propertiesToSkip = array())
	{
		$this->validatePropertyNotNull('streamUrl');
		return parent::validateForInsert($propertiesToSkip);
	}
		 
	/* (non-PHPdoc)
	 * @see KalturaObject::validateForUpdate($sourceObject, $propertiesToSkip)
	 */
	public function validateForUpdate($sourceObject, $propertiesToSkip = array())
	{
		if($this->streamUrl instanceof KalturaNullField)
		{
			throw new KalturaAPIException(KalturaErrors::PROPERTY_VALIDATION_CANNOT_BE_NULL, $this->getFormattedPropertyNameWithClassName('streamUrl'));
		}
		return parent::validateForUpdate($sourceObject, $propertiesToSkip);
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::toObject($object_to_fill, $props_to_skip)
	 */
	public function toObject($sourceObject = null, $propertiesToSkip = array())
	{
		if(is_null($sourceObject))
		{
			$sourceObject = new CameraScheduleResource();
		}
		
		return parent::toObject($sourceObject, $propertiesToSkip);
	}
}