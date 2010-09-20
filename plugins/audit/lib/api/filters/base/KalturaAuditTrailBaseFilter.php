<?php
/**
 * @package api
 * @subpackage filters.base
 * @abstract
 */
class KalturaAuditTrailBaseFilter extends KalturaFilter
{
	private $map_between_objects = array
	(
		"idEqual" => "_eq_id",
		"createdAtGreaterThanOrEqual" => "_gte_created_at",
		"createdAtLessThanOrEqual" => "_lte_created_at",
		"parsedAtGreaterThanOrEqual" => "_gte_parsed_at",
		"parsedAtLessThanOrEqual" => "_lte_parsed_at",
		"statusEqual" => "_eq_status",
		"statusIn" => "_in_status",
		"objectTypeEqual" => "_eq_object_type",
		"objectTypeIn" => "_in_object_type",
		"objectIdEqual" => "_eq_object_id",
		"objectIdIn" => "_in_object_id",
		"relatedObjectIdEqual" => "_eq_related_object_id",
		"relatedObjectIdIn" => "_in_related_object_id",
		"relatedObjectTypeEqual" => "_eq_related_object_type",
		"relatedObjectTypeIn" => "_in_related_object_type",
		"entryIdEqual" => "_eq_entry_id",
		"entryIdIn" => "_in_entry_id",
		"masterPartnerIdEqual" => "_eq_master_partner_id",
		"masterPartnerIdIn" => "_in_master_partner_id",
		"partnerIdEqual" => "_eq_partner_id",
		"partnerIdIn" => "_in_partner_id",
		"requestIdEqual" => "_eq_request_id",
		"requestIdIn" => "_in_request_id",
		"userIdEqual" => "_eq_user_id",
		"userIdIn" => "_in_user_id",
		"actionEqual" => "_eq_action",
		"actionIn" => "_in_action",
		"ksEqual" => "_eq_ks",
		"contextEqual" => "_eq_context",
		"contextIn" => "_in_context",
		"entryPointEqual" => "_eq_entry_point",
		"entryPointIn" => "_in_entry_point",
		"serverNameEqual" => "_eq_server_name",
		"serverNameIn" => "_in_server_name",
		"ipAddressEqual" => "_eq_ip_address",
		"ipAddressIn" => "_in_ip_address",
		"clientTagEqual" => "_eq_client_tag",
	);

	private $order_by_map = array
	(
		"+createdAt" => "+created_at",
		"-createdAt" => "-created_at",
		"+parsedAt" => "+parsed_at",
		"-parsedAt" => "-parsed_at",
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
	public $parsedAtGreaterThanOrEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $parsedAtLessThanOrEqual;

	/**
	 * 
	 * 
	 * @var KalturaAuditTrailStatus
	 */
	public $statusEqual;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $statusIn;

	/**
	 * 
	 * 
	 * @var KalturaAuditTrailObjectType
	 */
	public $objectTypeEqual;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $objectTypeIn;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $objectIdEqual;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $objectIdIn;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $relatedObjectIdEqual;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $relatedObjectIdIn;

	/**
	 * 
	 * 
	 * @var KalturaAuditTrailObjectType
	 */
	public $relatedObjectTypeEqual;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $relatedObjectTypeIn;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $entryIdEqual;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $entryIdIn;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $masterPartnerIdEqual;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $masterPartnerIdIn;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $partnerIdEqual;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $partnerIdIn;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $requestIdEqual;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $requestIdIn;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $userIdEqual;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $userIdIn;

	/**
	 * 
	 * 
	 * @var KalturaAuditTrailAction
	 */
	public $actionEqual;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $actionIn;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $ksEqual;

	/**
	 * 
	 * 
	 * @var KalturaAuditTrailContext
	 */
	public $contextEqual;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $contextIn;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $entryPointEqual;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $entryPointIn;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $serverNameEqual;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $serverNameIn;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $ipAddressEqual;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $ipAddressIn;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $clientTagEqual;
}
