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

			case 'kClipsVendorTaskData':
				$taskData = new KalturaClipsVendorTaskData();
				break;

			case 'kQuizVendorTaskData':
				$taskData = new KalturaQuizVendorTaskData();
				break;

			case 'kSummaryVendorTaskData':
				$taskData = new KalturaSummaryVendorTaskData();
				break;

			case 'kModerationVendorTaskData':
				$taskData = new KalturaModerationVendorTaskData();
				break;

			case 'kMetadataEnrichmentVendorTaskData':
				$taskData = new KalturaMetadataEnrichmentVendorTaskData();
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
