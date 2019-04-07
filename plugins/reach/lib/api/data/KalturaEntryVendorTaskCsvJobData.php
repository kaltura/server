<?php
/**
 * @package plugins.reach
 * @subpackage api.objects
 */
class KalturaEntryVendorTaskCsvJobData extends KalturaExportCsvJobData
{
	
	/**
	 * The filter should return the list of users that need to be specified in the csv.
	 *
	 * @var KalturaEntryVendorTaskFilter
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
			$dbData = new kEntryVendorTaskCsvJobData();

		return parent::toObject($dbData, $props_to_skip);
	}
	
}
