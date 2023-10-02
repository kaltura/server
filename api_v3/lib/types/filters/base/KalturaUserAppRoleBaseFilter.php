<?php
/**
 * @package api
 * @relatedService UserAppRoleService
 * @subpackage filters.base
 * @abstract
 */
abstract class KalturaUserAppRoleBaseFilter extends KalturaRelatedFilter
{
	/**
	 * The app-registry id to search for
	 *
	 * @var string
	 */
	public $appGuidEqual;
	
	/**
	 * Apps-registries ids csv list
	 *
	 * @var string
	 */
	public $appGuidIn;
	
	/**
	 * The user-role id to search for
	 *
	 * @var string
	 */
	public $userRoleIdEqual;
	
	/**
	 * Users-roles ids csv list
	 *
	 * @var string
	 */
	public $userRoleIdIn;
	
	/**
	 * Unix timestamp
	 *
	 * @var time
	 */
	public $createdAtLessThanOrEqual;
	
	/**
	 * Unix timestamp
	 *
	 * @var time
	 */
	public $createdAtGreaterThanOrEqual;
	
	/**
	 * Unix timestamp
	 *
	 * @var time
	 */
	public $updatedAtLessThanOrEqual;
	
	/**
	 * Unix timestamp
	 *
	 * @var time
	 */
	public $updatedAtGreaterThanOrEqual;
	
	static private $map_between_objects = array
	(
		"appGuidEqual" => "_eq_app_guid",
		"appGuidIn" => "_in_app_guid",
		"userRoleIdEqual" => "_eq_user_role_id",
		"userRoleIdIn" => "_in_user_role_id",
		"createdAtLessThanOrEqual" => "_lte_created_at",
		"createdAtGreaterThanOrEqual" => "_gte_created_at",
		"updatedAtLessThanOrEqual" => "_lte_updated_at",
		"updatedAtGreaterThanOrEqual" => "_gte_updated_at",
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
}

