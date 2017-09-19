<?php

/**
 * @package plugins.beacon
 * @subpackage api.objects
 */
class KalturaBeacon extends KalturaObject implements IFilterable
{
	/**
	 * Beacon id
	 *
	 * @var string
	 * @readonly
	 */
	public $id;
	
	/**
	 * Beacon indexType
	 *
	 * @var string
	 * @readonly
	 */
	public $indexType;
	
	/**
	 * Beacon update date as Unix timestamp (In seconds)
	 *
	 * @var time
	 * @readonly
	 * @filter gte,lte,order
	 */
	public $updatedAt;
	
	/**
	 * The object which this beacon belongs to
	 *
	 * @var KalturaBeaconObjectTypes
	 * @filter in
	 */
	public $relatedObjectType;
	
	/**
	 * @var string
	 * @filter in
	 */
	public $eventType;
	
	/**
	 * @var string
	 * @filter in,order
	 */
	public $objectId;
	
	/**
	 * @var string
	 */
	public $privateData;
	
	/**
	 * @var string
	 */
	public $rawData;
	
	private static $map_between_objects = array
	(
		'id',
		'indexType',
		'updatedAt' => "updated_at",
		'relatedObjectType' => 'related_object_type',
		'eventType' => 'event_type',
		'objectId' => "object_id",
		'privateData' => 'private_data',
		'rawData' => 'raw_data',
	);
	
	public function validateForInsert($propertiesToSkip = array())
	{
		$this->validatePropertyNotNull("eventType");
		$this->validatePropertyNotNull("objectId");
		$this->validatePropertyNotNull("relatedObjectType");
		return parent::validateForInsert($propertiesToSkip);
	}
	
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
	public function getExtraFilters()
	{
		return array();
	}
	
	public function getFilterDocs()
	{
		return array();
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::toInsertableObject()
	 */
	public function toInsertableObject($object_to_fill = null, $props_to_skip = array())
	{
		if (is_null($object_to_fill))
			$object_to_fill = new kBeacon();
		
		return parent::toInsertableObject($object_to_fill, $props_to_skip);
	}
	
	public function fromArray($source_array)
	{
		parent::fromArray($source_array);
		
		if (isset($source_array[kBeacon::FIELD_PRIVATE_DATA]))
			$this->privateData = json_encode($source_array[kBeacon::FIELD_PRIVATE_DATA]);
	}
}