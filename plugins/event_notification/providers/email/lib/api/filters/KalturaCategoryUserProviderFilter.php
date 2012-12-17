<?php
/**
 * @package api
 * @subpackage filters.base
 */
class KalturaCategoryUserProviderFilter extends KalturaFilter
{
	static private $map_between_objects = array
	(
		"userIdEqual" => "_eq_user_id",
		"userIdIn" => "_in_user_id",
		"statusEqual" => "_eq_status",
		"statusIn" => "_in_status",
		"createdAtGreaterThanOrEqual" => "_gte_created_at",
		"createdAtLessThanOrEqual" => "_lte_created_at",
		"updatedAtGreaterThanOrEqual" => "_gte_updated_at",
		"updatedAtLessThanOrEqual" => "_lte_updated_at",
		"updateMethodEqual" => "_eq_update_method",
		"updateMethodIn" => "_in_update_method",
		"permissionNamesMatchAnd" => "_matchand_permission_names",
		"permissionNamesMatchOr" => "_matchor_permission_names",
	);

	static private $order_by_map = array
	(
		"+createdAt" => "+created_at",
		"-createdAt" => "-created_at",
		"+updatedAt" => "+updated_at",
		"-updatedAt" => "-updated_at",
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

	public function getOrderByMap()
	{
		return array_merge(parent::getOrderByMap(), self::$order_by_map);
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::toObject()
	 */
	public function toObject($coreFilter = null, $props_to_skip = array()) 
	{
		if(is_null($coreFilter))
			$coreFilter = new categoryKuserFilter();
			
		return parent::toObject($coreFilter, $props_to_skip);
	}

	/**
	 * @var string
	 */
	public $userIdEqual;

	/**
	 * @var string
	 */
	public $userIdIn;

	/**
	 * @var KalturaCategoryUserPermissionLevel
	 */
	public $permissionLevelEqual;

	/**
	 * @var string
	 */
	public $permissionLevelIn;

	/**
	 * @var KalturaCategoryUserStatus
	 */
	public $statusEqual;

	/**
	 * @var string
	 */
	public $statusIn;

	/**
	 * @var int
	 */
	public $createdAtGreaterThanOrEqual;

	/**
	 * @var int
	 */
	public $createdAtLessThanOrEqual;

	/**
	 * @var int
	 */
	public $updatedAtGreaterThanOrEqual;

	/**
	 * @var int
	 */
	public $updatedAtLessThanOrEqual;

	/**
	 * @var KalturaUpdateMethodType
	 */
	public $updateMethodEqual;

	/**
	 * @var string
	 */
	public $updateMethodIn;

	/**
	 * @var string
	 */
	public $permissionNamesMatchAnd;

	/**
	 * @var string
	 */
	public $permissionNamesMatchOr;
}
