<?php
/**
 * @package plugins.elasticSearch
 * @subpackage api.objects
 */
abstract class KalturaESearchParams extends KalturaObject
{
	/**
	 * @var string
	 */
	public $objectStatuses;

	/**
	 * @var string
	 */
	public $objectId;

	/**
	 * @var KalturaESearchOrderBy
	 */
	public $orderBy;
	
	/**
	 * @var bool
	 */
	public $ignoreSynonym;

	/**
	 * @var string
	 */
	public $objectIds;


	private static $mapBetweenObjects = array
	(
		"objectStatuses",
		"objectId",
		"orderBy",
		"ignoreSynonym",
		"objectIds"
	);

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

		if ($this->objectId && $this->objectIds)
		{
			throw new KalturaAPIException(KalturaESearchErrors::OBJECTID_AND_OBJECTIDS_NOT_ALLOWED_SIMULTANEOUSLY);
		}

		return parent::toObject($object_to_fill, $props_to_skip);
	}

	protected static function validateSearchOperator($searchOperator)
	{
		if (!$searchOperator)
		{
			throw new KalturaAPIException(KalturaESearchErrors::EMPTY_SEARCH_OPERATOR_NOT_ALLOWED);
		}

		if (!$searchOperator->operator)
		{
			$searchOperator->operator = KalturaSearchOperatorType::SEARCH_AND;
		}
	}

}
