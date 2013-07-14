<?php
/**
 * @package plugins.widevine
 * @subpackage api.objects
 */
class KalturaWidevineFlavorAsset extends KalturaFlavorAsset 
{
	/**
	 * License distribution window start date 
	 * 
	 * @var int
	 */
	public $widevineDistributionStartDate;
	
	/**
	 * License distribution window end date
	 * 
	 * @var int
	 */
	public $widevineDistributionEndDate;
	
	/**
	 * Widevine unique asset id
	 * 
	 * @var int
	 */
	public $widevineAssetId;
	
	
	/*
	 * mapping between the field on this object (on the left) and the setter/getter on the entry object (on the right)  
	 */
	private static $map_between_objects = array(
		'widevineDistributionStartDate',
		'widevineDistributionEndDate',
		'widevineAssetId',
	 );
		 
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
	public function toObject($object = null, $skip = array())
	{
		if(is_null($object))
			$object = new WidevineFlavorAsset();
		
		parent::toObject($object, $skip);
		return $object;
	}
}