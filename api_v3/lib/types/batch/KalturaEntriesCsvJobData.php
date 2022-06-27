<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaEntriesCsvJobData extends KalturaMappedObjectsCsvJobData
{
	
	/**
	 * The filter should return the list of entries that need to be specified in the csv.
	 * @var KalturaBaseEntryFilter
	 */
	public $filter;

	private static $map_between_objects = array
	(
		'filter',
	);
	
	/* (non-PHPdoc)
	 * @see KalturaObject::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

	/* (non-PHPdoc)
	 * @see KalturaObject::toObject()
	 */
	public function toObject($dbData = null, $props_to_skip = array())
	{
		if(is_null($dbData))
			$dbData = new kEntriesCsvJobData();

		return parent::toObject($dbData, $props_to_skip);
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::fromObject()
	 */
	public function doFromObject($dbData, KalturaDetachedResponseProfile $responseProfile = null)
	{
		/* @var $dbData kEntriesCsvJobData */
		$filter = $dbData->getFilter();
		$filterType = get_class($filter);
		switch ($filterType)
		{
			case 'entryFilter':
				$this->filter = new KalturaBaseEntryFilter();
				break;
			
			case 'mediaEntryFilter':
				$this->filter = new KalturaMediaEntryFilter();
				break;
			
			default:
				$this->filter = KalturaPluginManager::loadObject('KalturaFilter', $filterType);
		}
		if ($this->filter)
		{
			$this->filter->fromObject($filter);
		}
		
		parent::doFromObject($dbData, $responseProfile);
	}
}
