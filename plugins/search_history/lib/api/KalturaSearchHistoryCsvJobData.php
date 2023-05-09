<?php
/**
 * @package plugins.searchHistory
 * @subpackage api.objects
 */
class KalturaSearchHistoryCsvJobData extends KalturaExportCsvJobData
{
	/**
	 * @var KalturaESearchHistoryFilter
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
		if (is_null($dbData))
		{
			$dbData = new kSearchHistoryCsvJobData();
		}

		return parent::toObject($dbData, $props_to_skip);
	}
}