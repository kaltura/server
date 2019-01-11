<?php
/**
 * @package plugins.bulkUploadFilter
 * @subpackage api.objects
 */
class KalturaBulkUploadResultJob extends KalturaBulkUploadResult
{
	
	/**
	 * ID of object being processed by the job
	 * @var int
	 */
	public $jobObjectId;
	
	private static $mapBetweenObjects = array
	(
		'jobObjectId',
	);
	
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$mapBetweenObjects);
	}
	
	/* (non-PHPdoc)
     * @see KalturaBulkUploadResult::toInsertableObject()
     */
	public function toInsertableObject ( $object_to_fill = null , $props_to_skip = array() )
	{
		return parent::toInsertableObject(new BulkUploadResultJob(), $props_to_skip);
	}
}