<?php
/**
 * @package plugins.elasticSearch
 * @subpackage api.objects
 */
class KalturaESearchAggregationBucket extends KalturaObject
{
	/**
	 * @var KalturaString
	 */
	public $value;

	/**
	 * @var int
	 */
	public $count;


	private static $map_between_objects = array(
		'value'=>'key',
		'count'=>'doc_count'
	);

	protected function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
}
