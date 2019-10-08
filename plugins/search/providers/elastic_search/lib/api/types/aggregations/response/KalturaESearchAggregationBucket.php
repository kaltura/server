<?php
/**
 * @package plugins.elasticSearch
 * @subpackage api.objects
 */
class KalturaESearchAggregationBucket extends KalturaObject
{
	/**
	 * @var string
	 */
	public $value;

	/**
	 * @var int
	 */
	public $count;


	private static $map_between_objects = array(
		'value'=>ESearchAggregations::KEY,
		'count'=>ESearchAggregations::DOC_COUNT
	);

	protected function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
}
