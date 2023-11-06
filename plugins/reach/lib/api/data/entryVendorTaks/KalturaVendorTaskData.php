<?php
/**
 * @package plugins.reach
 * @subpackage api.objects
 * @relatedService EntryVendorTaskService
 */
abstract class KalturaVendorTaskData extends KalturaObject implements IApiObjectFactory
{
	
	/**
	 * The duration of the entry for which the task was created for in milliseconds
	 * @var int
	 * @readonly
	 */
	public $entryDuration;

	/**
	 * The duration of the entry processed by the vendor in milliseconds
	 * @var int
	 * @requiresPermission insert, update
	 */
	public $processedEntryDuration;
	
	private static $map_between_objects = array
	(
		'entryDuration',
		'processedEntryDuration'
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
			case 'kAlignmentVendorTaskData':
				$taskData = new KalturaAlignmentVendorTaskData();
				break;

			case 'kTranslationVendorTaskData':
				$taskData = new KalturaTranslationVendorTaskData();
				break;

			case 'kIntelligentTaggingVendorTaskData':
				$taskData = new KalturaIntelligentTaggingVendorTaskData();
				break;

			case 'kScheduledVendorTaskData':
				$taskData = new KalturaScheduledVendorTaskData();
				break;
		}
		
		if ($taskData)
			/* @var $object KalturaVendorTaskData */
			$taskData->fromObject($sourceObject, $responseProfile);
		
		return $taskData;
	}

	public function validateCatalogLimitations($vendorCatalogItem)
	{

	}
}
