<?php
/**
 * @package plugins.reach
 * @subpackage api.objects
 * @relatedService EntryVendorTaskService
 */
class KalturaScheduledVendorTaskData extends KalturaVendorTaskData
{
	/**
	 * TODO
	 * @var time
	 * @insertonly
	 */
	public $startDate;

	/**
	 * TODO
	 * @var time
	 * @insertonly
	 */
	public $endDate;

	/**
	 * TODO
	 * @var int
	 * @readonly
	 */
	public $scheduleEventId;

	private static $map_between_objects = array
	(
		'scheduleEventId',
		'startDate',
		'endDate',
	);

	/* (non-PHPdoc)
	 * @see KalturaCuePoint::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

	/* (non-PHPdoc)
 	 * @see KalturaObject::toObject($object_to_fill, $props_to_skip)
 	 */
	public function toObject($dbObject = null, $propsToSkip = array())
	{
		if (!$dbObject) {
			$dbObject = new kScheduledVendorTaskData();
		}
		return parent::toObject($dbObject, $propsToSkip);
	}

	/* (non-PHPdoc)
 	 * @see KalturaObject::toInsertableObject()
 	 */
	public function toInsertableObject($object_to_fill = null, $props_to_skip = array())
	{
		if (is_null($object_to_fill)) {
			$object_to_fill = new kScheduledVendorTaskData();
		}
		return parent::toInsertableObject($object_to_fill, $props_to_skip);
	}

	public function validateForInsert($propertiesToSkip = array())
	{
		$this->validatePropertyNotNull('startDate');
		$this->validatePropertyNotNull('endDate');
		return parent::validateForInsert($propertiesToSkip);
	}

}
