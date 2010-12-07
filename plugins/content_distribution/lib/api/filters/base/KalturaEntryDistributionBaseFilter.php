<?php
/**
 * @package api
 * @subpackage filters.base
 * @abstract
 */
class KalturaEntryDistributionBaseFilter extends KalturaFilter
{
	private $map_between_objects = array
	(
		"idEqual" => "_eq_id",
		"idIn" => "_in_id",
		"createdAtGreaterThanOrEqual" => "_gte_created_at",
		"createdAtLessThanOrEqual" => "_lte_created_at",
		"updatedAtGreaterThanOrEqual" => "_gte_updated_at",
		"updatedAtLessThanOrEqual" => "_lte_updated_at",
		"submittedAtGreaterThanOrEqual" => "_gte_submitted_at",
		"submittedAtLessThanOrEqual" => "_lte_submitted_at",
		"entryIdEqual" => "_eq_entry_id",
		"entryIdIn" => "_in_entry_id",
		"distributionProfileIdEqual" => "_eq_distribution_profile_id",
		"distributionProfileIdIn" => "_in_distribution_profile_id",
		"statusEqual" => "_eq_status",
		"statusIn" => "_in_status",
		"dirtyStatusEqual" => "_eq_dirty_status",
		"dirtyStatusIn" => "_in_dirty_status",
		"sunriseGreaterThanOrEqual" => "_gte_sunrise",
		"sunriseLessThanOrEqual" => "_lte_sunrise",
		"sunsetGreaterThanOrEqual" => "_gte_sunset",
		"sunsetLessThanOrEqual" => "_lte_sunset",
	);

	private $order_by_map = array
	(
		"+createdAt" => "+created_at",
		"-createdAt" => "-created_at",
		"+updatedAt" => "+updated_at",
		"-updatedAt" => "-updated_at",
		"+submittedAt" => "+submitted_at",
		"-submittedAt" => "-submitted_at",
		"+sunrise" => "+sunrise",
		"-sunrise" => "-sunrise",
		"+sunset" => "+sunset",
		"-sunset" => "-sunset",
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
	public $submittedAtGreaterThanOrEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $submittedAtLessThanOrEqual;

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
	public $distributionProfileIdEqual;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $distributionProfileIdIn;

	/**
	 * 
	 * 
	 * @var int
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
	 * @var int
	 */
	public $dirtyStatusEqual;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $dirtyStatusIn;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $sunriseGreaterThanOrEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $sunriseLessThanOrEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $sunsetGreaterThanOrEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $sunsetLessThanOrEqual;
}
