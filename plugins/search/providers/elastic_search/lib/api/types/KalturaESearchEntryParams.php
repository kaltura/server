<?php
/**
 * @package plugins.elasticSearch
 * @subpackage api.objects
 */
class KalturaESearchEntryParams extends KalturaESearchParams
{
	/**
	 * @var KalturaESearchEntryOperator
	 */
	public $searchOperator;

	/**
	 * @var KalturaESearchAggregation
	 */
	public $aggregations;

	/**
	 * @var string
	 */
	public $objectIds;


	private static $mapBetweenObjects = array
	(
		"searchOperator","aggregations","objectIds"
	);

	protected function initStatuses()
	{
		$statuses = explode(',', $this->objectStatuses);
		$enumType = KalturaEntryStatus::getEnumClass();

		$finalStatuses = array();
		foreach($statuses as $status)
		{
			$finalStatuses[] = kPluginableEnumsManager::apiToCore($enumType, $status);
		}
		return implode(',', $finalStatuses);
	}

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$mapBetweenObjects);
	}

	public function toObject($object_to_fill = null, $props_to_skip = array())
	{
		if (!$object_to_fill)
		{
			$object_to_fill = new ESearchParams();
		}

		self::validateSearchOperator($this->searchOperator);

		if ($this->objectId && $this->objectIds)
		{
			throw new KalturaAPIException(KalturaESearchErrors::OBJECTID_AND_OBJECTIDS_NOT_ALLOWED_SIMULTANEOUSLY);
		}

		if (!empty($this->objectStatuses))
		{
			$this->objectStatuses = $this->initStatuses();
		}

		return parent::toObject($object_to_fill, $props_to_skip);
	}

}
