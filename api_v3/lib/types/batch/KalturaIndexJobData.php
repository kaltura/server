<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaIndexJobData extends KalturaJobData
{
	/**
	 * The filter should return the list of objects that need to be reindexed.
	 * @var KalturaFilter
	 */
	public $filter;
	
	/**
	 * Indicates the last id that reindexed, used when the batch crached, to re-run from the last crash point.
	 * @var int
	 */
	public $lastIndexId;

	/**
	 * Indicates the last depth that reindexed, used when the batch crached, to re-run from the last crash point.
	 * @var int
	 */
	public $lastIndexDepth;
	
	/**
	 * Indicates that the object columns and attributes values should be recalculated before reindexed.
	 * @var bool
	 */
	public $shouldUpdate;
	
	private static $map_between_objects = array
	(
		"lastIndexId" ,
		"shouldUpdate" ,
		"lastIndexDepth",
	);

	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}

	/**
	 * @param string $subType is the provider type
	 * @return int
	 */
	public function toSubType($subType)
	{
		return kPluginableEnumsManager::apiToCore('IndexObjectType', $subType);
	}

	/**
	 * @param int $subType
	 * @return string
	 */
	public function fromSubType($subType)
	{
		return kPluginableEnumsManager::coreToApi('IndexObjectType', $subType);
	}
	
	public function toObject($dbData = null, $props_to_skip = array()) 
	{
		if(is_null($dbData))
			$dbData = new kIndexJobData();
			
		return parent::toObject($dbData, $props_to_skip);
	}
	
	public function doFromObject($dbData, KalturaDetachedResponseProfile $responseProfile = null) 
	{
		/* @var $dbData kIndexJobData */
		$filter = $dbData->getFilter();
		$filterType = get_class($filter);
		switch($filterType)
		{
			case 'entryFilter':
				$this->filter = new KalturaBaseEntryFilter();
				break;
				
			case 'categoryFilter':
				$this->filter = new KalturaCategoryFilter();
				break;
			
			case 'categoryEntryFilter':
				$this->filter = new KalturaCategoryEntryFilter();
				break;
				
			case 'categoryKuserFilter':
				$this->filter = new KalturaCategoryUserFilter();
				break;
			
			case 'kuserFilter':
				$this->filter = new KalturaUserFilter();
				break;
				
			default:
				$this->filter = KalturaPluginManager::loadObject('KalturaFilter', $filterType);
		}
		if($this->filter)
			$this->filter->fromObject($filter);
			
		parent::doFromObject($dbData, $responseProfile);
	}
}
