<?php
/**
 * @package api
 * @subpackage filters.base
 * @abstract
 */
class KalturaControlPanelCommandBaseFilter extends KalturaFilter
{
	private $map_between_objects = array
	(
		"idEqual" => "_eq_id",
		"idIn" => "_in_id",
		"createdAtGreaterThanOrEqual" => "_gte_created_at",
		"createdAtLessThanOrEqual" => "_lte_created_at",
		"createdByIdEqual" => "_eq_created_by_id",
		"typeEqual" => "_eq_type",
		"typeIn" => "_in_type",
		"targetTypeEqual" => "_eq_target_type",
		"targetTypeIn" => "_in_target_type",
		"statusEqual" => "_eq_status",
		"statusIn" => "_in_status",
	);

	private $order_by_map = array
	(
		"+createdAt" => "+created_at",
		"-createdAt" => "-created_at",
		"+updatedAt" => "+updated_at",
		"-updatedAt" => "-updated_at",
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), $this->map_between_objects);
	}

	public function getOrderByMap()
	{
		return array_merge(parent::getOrderByMap(), $this->order_by_map);
	}

	/**
	 * 
	 * 
	 * @var int
	 */
	public $idEqual;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $idIn;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $createdAtGreaterThanOrEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $createdAtLessThanOrEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $createdByIdEqual;

	/**
	 * 
	 * 
	 * @var KalturaControlPanelCommandType
	 */
	public $typeEqual;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $typeIn;

	/**
	 * 
	 * 
	 * @var KalturaControlPanelCommandTargetType
	 */
	public $targetTypeEqual;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $targetTypeIn;

	/**
	 * 
	 * 
	 * @var KalturaControlPanelCommandStatus
	 */
	public $statusEqual;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $statusIn;
}
