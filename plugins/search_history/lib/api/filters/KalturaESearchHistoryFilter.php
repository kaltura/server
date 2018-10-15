<?php
/**
 * @package plugins.searchHistory
 * @subpackage api.filters
 */
class KalturaESearchHistoryFilter extends KalturaESearchBaseFilter
{

	/**
	 * @var string
	 */
	public $searchTermStartsWith;

	/**
	 * @var string
	 */
	public $searchedObjectIn;

	private static $map_between_objects = array(
		'searchTermStartsWith',
		'searchedObjectIn',
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

	public function toObject($object_to_fill = null, $props_to_skip = array())
	{
		if (!$object_to_fill)
			$object_to_fill = new ESearchHistoryFilter();
		return parent::toObject($object_to_fill, $props_to_skip);
	}

	public function getListResponse()
	{
		$coreFilter = $this->toObject();
		list($coreObjects, $totalCount) = $coreFilter->execQueryFromFilter();
		$response = new KalturaESearchHistoryListResponse();
		$response->objects = KalturaESearchHistoryArray::fromDbArray($coreObjects);
		$response->totalCount = $totalCount;
		return $response;
	}

}
