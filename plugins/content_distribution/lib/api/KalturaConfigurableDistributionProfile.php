<?php
/**
 * @package plugins.contentDistribution
 * @subpackage api.objects
 * @abstract
 */
abstract class KalturaConfigurableDistributionProfile extends KalturaDistributionProfile
{

	/**
	 * @var KalturaDistributionFieldConfigArray
	 */
	public $fieldConfigArray;
	
	/**
	 * @var KalturaExtendingItemMrssParameterArray
	 */
	public $itemXpathsToExtend;
	
	/**
	 * When checking custom XSLT conditions using the fieldConfigArray - address only categories associated with the entry via the categoryEntry object
	 * @var bool
	 */
	public $useCategoryEntries;
	
	/*
	 * mapping between the field on this object (on the left) and the setter/getter on the object (on the right)  
	 */
	private static $map_between_objects = array 
	 (
		'fieldConfigArray',
	 	'itemXpathsToExtend',
	 );
	 
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

	public function toObject($dbObject = null, $skip = array())
	{
		/* @var $dbObject ConfigurableDistributionProfile */
		parent::toObject($dbObject, $skip);
		
		if ($this->useCategoryEntries)
		{
			$features = $dbObject->getExtendedFeatures();
			$features[] = ObjectFeatureType::CATEGORY_ENTRIES;
			$dbObject->setExtendedFeatures(array_unique($features));
		}
		
		return $dbObject;
	}
	
	protected function doFromObject($srcObj, KalturaDetachedResponseProfile $responseProfile = null)
	{
		parent::doFromObject($srcObj);
		
		$features = $srcObj->getExtendedFeatures ();
		if (in_array (ObjectFeatureType::CATEGORY_ENTRIES, $features))
		{
			$this->useCategoryEntries = true;
		}
	}
}