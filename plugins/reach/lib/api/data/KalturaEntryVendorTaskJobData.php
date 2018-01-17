<?php
/**
 * @package plugins.reach
 * @subpackage api.objects
 */
class KalturaEntryVendorTaskJobData extends KalturaObject
{
	/**
	 *  @var KalturaVendorServiceType
	 */
	public $serviceType;
	
	/**
	 * @var KalturaVendorServiceFeature
	 */
	public $serviceFeature;
	
	/**
	 * @var KalturaVendorServiceTurnAroundTime
	 */
	public $turnAroundTime;
	
	
	private static $map_between_objects = array (
		'serviceType',
		'serviceFeature',
		'turnAroundTime',
	);
	
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
			$dbObject = new kEntryVendorTaskJobData();
		}
		
		return parent::toObject($dbObject, $propsToSkip);
	}
	
	/* (non-PHPdoc)
  	 * @see IApiObjectFactory::getInstance($sourceObject, KalturaDetachedResponseProfile $responseProfile)
  	 */
	public static function getInstanceByType($sourceObject, KalturaDetachedResponseProfile $responseProfile)
	{
		$object = null;
		switch($sourceObject->getServiceFeature())
		{
			case VendorServiceFeature::CAPTIONS:
				$object = new KalturaCaptionsEntryVendorTaskJobData();
				break;
			
			case VendorServiceFeature::TRANSLATION:
				$object = new KalturaTranslationEntryVendorTaskJobData();
				break;
			
		}
		
		$object->fromObject($sourceObject, $responseProfile);
		return $object;
	}
}