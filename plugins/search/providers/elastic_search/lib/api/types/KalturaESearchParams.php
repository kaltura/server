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


	private static $mapBetweenObjects = array
	(
		"objectStatuses",
		"objectId",
		"orderBy",
		"ignoreSynonym",
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
