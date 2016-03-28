<?php
/**
 * @package plugins.ask
 * @subpackage api.filters.base
 * @abstract
 */
abstract class KalturaAnswerCuePointBaseFilter extends KalturaCuePointFilter
{
	static private $map_between_objects = array
	(
		"parentIdEqual" => "_eq_parent_id",
		"parentIdIn" => "_in_parent_id",
		"askUserEntryIdEqual" => "_eq_ask_user_entry_id",
		"askUserEntryIdIn" => "_in_ask_user_entry_id",
	);

	static private $order_by_map = array
	(
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

	public function getOrderByMap()
	{
		return array_merge(parent::getOrderByMap(), self::$order_by_map);
	}

	/**
	 * @var string
	 */
	public $parentIdEqual;

	/**
	 * @var string
	 */
	public $parentIdIn;
	
	/**
	 * @var string
	 */
	public $askUserEntryIdEqual;
	

	/**
	 * @var string
	 */
	public $askUserEntryIdIn;
}
