<?php
/**
 * @package api
 * @subpackage filters.base
 * @abstract
 */
abstract class KalturaWidgetBaseFilter extends KalturaFilter
{
	static private $map_between_objects = array
	(
		"idEqual" => "_eq_id",
		"idIn" => "_in_id",
		"sourceWidgetIdEqual" => "_eq_source_widget_id",
		"rootWidgetIdEqual" => "_eq_root_widget_id",
		"partnerIdEqual" => "_eq_partner_id",
		"entryIdEqual" => "_eq_entry_id",
		"uiConfIdEqual" => "_eq_ui_conf_id",
		"createdAtGreaterThanOrEqual" => "_gte_created_at",
		"createdAtLessThanOrEqual" => "_lte_created_at",
		"updatedAtGreaterThanOrEqual" => "_gte_updated_at",
		"updatedAtLessThanOrEqual" => "_lte_updated_at",
		"partnerDataLike" => "_like_partner_data",
	);

	static private $order_by_map = array
	(
		"+createdAt" => "+created_at",
		"-createdAt" => "-created_at",
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
	public $idEqual;

	/**
	 * @var string
	 */
	public $idIn;

	/**
	 * @var string
	 */
	public $sourceWidgetIdEqual;

	/**
	 * @var string
	 */
	public $rootWidgetIdEqual;

	/**
	 * @var int
	 */
	public $partnerIdEqual;

	/**
	 * @var string
	 */
	public $entryIdEqual;

	/**
	 * @var int
	 */
	public $uiConfIdEqual;

	/**
	 * @var time
	 */
	public $createdAtGreaterThanOrEqual;

	/**
	 * @var time
	 */
	public $createdAtLessThanOrEqual;

	/**
	 * @var time
	 */
	public $updatedAtGreaterThanOrEqual;

	/**
	 * @var time
	 */
	public $updatedAtLessThanOrEqual;

	/**
	 * @var string
	 */
	public $partnerDataLike;
}
