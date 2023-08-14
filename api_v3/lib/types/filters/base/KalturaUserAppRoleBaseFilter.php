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
	 * @var string the app-registry id to search for
	 */
	public $appGuidEqual;
	
	/**
	 * @var string apps-registries ids csv list
	 */
	public $appGuidIn;
	
	/**
	 * @var string the user-role id to search for
	 */
	public $userRoleIdEqual;
	
	/**
	 * @var string users-roles ids csv list
	 */
	public $userRoleIdIn;
	
	/**
	 * @var time
	 */
	public $createdAtLessThanOrEqual;
	
	/**
	 * @var time
	 */
	public $createdAtGreaterThanOrEqual;
	
	/**
	 * @var time
	 */
	public $updatedAtLessThanOrEqual;
	
	/**
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
