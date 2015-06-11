<?php
/**
 * @package api
 * @subpackage filters.base
 * @abstract
 */
abstract class KalturaUserEntryBaseFilter extends KalturaFilter
{
	static private $map_between_objects = array
	(
		"idEqual" => "_eq_id",
		"idIn" => "_in_id",
		"idNotIn" => "_notin_id",
		"entryIdEqual" => "_eq_entry_id",
		"entryIdIn" => "_in_entry_id",
		"entryIdNotIn" => "_notin_entry_id",
		"userIdEqual" => "_eq_user_id",
		"userIdIn" => "_in_user_id",
		"userIdNotIn" => "_notin_user_id",
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
	 * @var int
	 */
	public $idEqual;

	/**
	 * @var string
	 */
	public $idIn;

	/**
	 * @var string
	 */
	public $idNotIn;

	/**
	 * @var string
	 */
	public $entryIdEqual;

	/**
	 * @var string
	 */
	public $entryIdIn;

	/**
	 * @var string
	 */
	public $entryIdNotIn;

	/**
	 * @var int
	 */
	public $userIdEqual;

	/**
	 * @var string
	 */
	public $userIdIn;

	/**
	 * @var string
	 */
	public $userIdNotIn;
}
