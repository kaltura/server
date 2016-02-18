<?php
/**
 * @package plugins.comcastMrssDistribution
 * @subpackage api.objects
 */
class KalturaComcastMrssDistributionProfile extends KalturaConfigurableDistributionProfile
{	
	/**
	 * @var int
	 */
	public $metadataProfileId;
	
	/**
	 * @readonly
	 * @var string
	 */
	public $feedUrl;
	
	/**
	 * @var string
	 */
	public $feedTitle;
	
	/**
	 * @var string
	 */
	public $feedLink;
	
	/**
	 * @var string
	 */
	public $feedDescription;
	
	/**
	 * @var string
	 */
	public $feedLastBuildDate;
	
	/**
	 * @var string
	 */
	public $itemLink;

	/**
	 * @var KalturaKeyValueArray
	 */
	public $cPlatformTvSeries;
	
	/**
	 * @var string
	 */
	public $cPlatformTvSeriesField;
	
	/**
	 * @var bool
	 */
	public $shouldIncludeCuePoints;
	
	/**
	 * @var bool
	 */
	public $shouldIncludeCaptions;
	
	/**
	 * @var bool
	 */
	public $shouldAddThumbExtension;
	
	/*
	 * mapping between the field on this object (on the left) and the setter/getter on the object (on the right)  
	 */
	private static $map_between_objects = array 
	(
		'metadataProfileId',
		'feedUrl',
		'feedTitle',
		'feedLink',
		'feedDescription',
		'feedLastBuildDate',
		'itemLink',
		'cPlatformTvSeries',
		'cPlatformTvSeriesField',
		'shouldIncludeCuePoints',
		'shouldIncludeCaptions',
		'shouldAddThumbExtension',
	);
	
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
	public function doFromObject($sourceObject, KalturaDetachedResponseProfile $responseProfile = null)
	{
		/* @var $sourceObject ComcastMrssDistributionProfile */
		parent::doFromObject($sourceObject, $responseProfile);
		
		$keyValArray = new KalturaKeyValueArray();
		$array = $sourceObject->getcPlatformTvSeries();
		if (is_array($array))
		{
			foreach($array as $key => $val)
			{
				$keyVal = new KalturaKeyValue();
				$keyVal->key = $key;
				$keyVal->value = $val;
				$keyValArray[] = $keyVal;
			}
		}
		$this->cPlatformTvSeries = $keyValArray;
	}
		
	public function toObject($object = null, $skip = array())
	{
		/* @var $object ComcastMrssDistributionProfile */
		if(is_null($object))
			$object = new ComcastMrssDistributionProfile();
		
		$object = parent::toObject($object, $skip);
		
		$array = array();
		if ($this->cPlatformTvSeries instanceof KalturaKeyValueArray)
		{
			foreach($this->cPlatformTvSeries as $keyVal)
			{
				/* @var $keyVal KalturaKeyValue */
				$array[$keyVal->key] = $keyVal->value; 
			}
		}
		$object->setcPlatformTvSeries($array);
		
		return $object;
	}
}