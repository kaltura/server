<?php
/**
 * @package api
 * @subpackage filters.base
 * @abstract
 */
class KalturaBaseJobBaseFilter extends KalturaFilter
{
	private $map_between_objects = array
	(
		"idEqual" => "_eq_id",
		"idGreaterThanOrEqual" => "_gte_id",
		"partnerIdEqual" => "_eq_partner_id",
		"partnerIdIn" => "_in_partner_id",
		"partnerIdNotIn" => "_notin_partner_id",
		"createdAtGreaterThanOrEqual" => "_gte_created_at",
		"createdAtLessThanOrEqual" => "_lte_created_at",
		"updatedAtGreaterThanOrEqual" => "_gte_updated_at",
		"updatedAtLessThanOrEqual" => "_lte_updated_at",
		"processorExpirationGreaterThanOrEqual" => "_gte_processor_expiration",
		"processorExpirationLessThanOrEqual" => "_lte_processor_expiration",
		"executionAttemptsGreaterThanOrEqual" => "_gte_execution_attempts",
		"executionAttemptsLessThanOrEqual" => "_lte_execution_attempts",
		"lockVersionGreaterThanOrEqual" => "_gte_lock_version",
		"lockVersionLessThanOrEqual" => "_lte_lock_version",
	);

	private $order_by_map = array
	(
		"+createdAt" => "+created_at",
		"-createdAt" => "-created_at",
		"+updatedAt" => "+updated_at",
		"-updatedAt" => "-updated_at",
		"+processorExpiration" => "+processor_expiration",
		"-processorExpiration" => "-processor_expiration",
		"+executionAttempts" => "+execution_attempts",
		"-executionAttempts" => "-execution_attempts",
		"+lockVersion" => "+lock_version",
		"-lockVersion" => "-lock_version",
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
	public $idGreaterThanOrEqual;

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
	public $partnerIdNotIn;

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
	public $updatedAtGreaterThanOrEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $updatedAtLessThanOrEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $processorExpirationGreaterThanOrEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $processorExpirationLessThanOrEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $executionAttemptsGreaterThanOrEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $executionAttemptsLessThanOrEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $lockVersionGreaterThanOrEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $lockVersionLessThanOrEqual;
}
