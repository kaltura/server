<?php
/**
 * @package plugins.reach
 * @subpackage api.objects
 * @relatedService EntryVendorTaskService
 */
class KalturaAlignmentVendorTaskData extends KalturaVendorTaskData
{
	/**
	 * The id of the transcript object the vendor should use while runing the alignment task
	 * @var string
	 */
	public $transcriptAssetId;
	
	private static $map_between_objects = array
	(
		'transcriptAssetId'
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
		if (!$dbObject)
		{
			$dbObject = new kAlignmentVendorTaskData();
		}
		
		return parent::toObject($dbObject, $propsToSkip);
	}
	
	/* (non-PHPdoc)
 	 * @see KalturaObject::toInsertableObject()
 	 */
	public function toInsertableObject($object_to_fill = null, $props_to_skip = array())
	{
		if (is_null($object_to_fill))
			$object_to_fill = new kAlignmentVendorTaskData();
		
		return parent::toInsertableObject($object_to_fill, $props_to_skip);
	}
	
	public function validateForInsert($propertiesToSkip = array())
	{
		$this->validatePropertyNotNull("transcriptAssetId");
		return parent::validateForInsert($propertiesToSkip);
	}
}
