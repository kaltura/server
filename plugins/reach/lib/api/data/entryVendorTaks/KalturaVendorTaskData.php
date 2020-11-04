<?php
/**
 * @package plugins.reach
 * @subpackage api.objects
 * @relatedService EntryVendorTaskService
 */
class KalturaVendorTaskData extends KalturaObject implements IApiObjectFactory
{
	
	/**
	 * The duration of the entry for which the task was created for in milliseconds
	 * @var int
	 * @readonly
	 */
	public $entryDuration;
	
	private static $map_between_objects = array
	(
		'entryDuration',
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
			$dbObject = new kVendorTaskData();
		}
		
		return parent::toObject($dbObject, $propsToSkip);
	}
	
	/* (non-PHPdoc)
 	 * @see IApiObjectFactory::getInstance($sourceObject, KalturaDetachedResponseProfile $responseProfile)
 	 */
	public static function getInstance($sourceObject, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$taskDataType = get_class($sourceObject);
		$taskData = null;
		switch ($taskDataType)
		{
			case 'kVendorTaskData':
				$taskData = new KalturaVendorTaskData();
				break;
			
			case 'kAlignmentVendorTaskData':
				$taskData = new KalturaAlignmentVendorTaskData();
				break;

			case 'kTranslationVendorTaskData':
				$taskData = new KalturaTranslationVendorTaskData();
				break;
		}
		
		if ($taskData)
			/* @var $object KalturaVendorTaskData */
			$taskData->fromObject($sourceObject, $responseProfile);
		
		return $taskData;
	}
}
