<?php
/**
 * Configuration for extended item in the Kaltura MRSS feeds
 *
 * @package api
 * @subpackage objects
 */
abstract class KalturaObjectIdentifier extends KalturaObject
{
	/**
	 * Comma separated string of enum values denoting which features of the item need to be included in the MRSS 
	 * @dynamicType KalturaObjectFeatureType
	 * @var string
	 */
	public $extendedFeatures;
	
	
	private static $map_between_objects = array(
	);
	
	/* (non-PHPdoc)
	 * @see KalturaObject::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::toObject($object_to_fill, $props_to_skip)
	 */
	public function toObject ($dbObject = null, $propsToSkip = null)
	{
		if (!$dbObject)
		{
			return null;
		}
		/* @var $dbObject kObjectIdentifier */		
		if ($this->extendedFeatures){
			$apiFeaturesArray = explode(",", $this->extendedFeatures);
			$coreFeatureArray = array();
			foreach ($apiFeaturesArray as $feature){
				$coreFeatureArray[] = kPluginableEnumsManager::apiToCore('ObjectFeatureType', $feature);
			}
			$dbObject->setExtendedFeatures($coreFeatureArray);
		}
		return parent::toObject($dbObject, $propsToSkip);
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::fromObject()
	 */
	public function fromObject($dbObject)
	{
		parent::fromObject($dbObject);
		if (is_array($dbObject->getExtendedFeatures())){
			$apiFeaturesArray = array();
			foreach ($dbObject->getExtendedFeatures() as $feature){
				$apiFeaturesArray[] = kPluginableEnumsManager::coreToApi('ObjectFeatureType', $feature);
			}
			$this->extendedFeatures = implode(',',$apiFeaturesArray);
		}
	}

}