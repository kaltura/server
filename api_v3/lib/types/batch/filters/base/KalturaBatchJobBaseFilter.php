<?php
/**
 * @package api
 * @subpackage filters.base
 * @abstract
 */
abstract class KalturaBatchJobBaseFilter extends KalturaFilter
{
	static private $map_between_objects = array
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
		"executionAttemptsGreaterThanOrEqual" => "_gte_execution_attempts",
		"executionAttemptsLessThanOrEqual" => "_lte_execution_attempts",
		"lockVersionGreaterThanOrEqual" => "_gte_lock_version",
		"lockVersionLessThanOrEqual" => "_lte_lock_version",
		"entryIdEqual" => "_eq_entry_id",
		"jobTypeEqual" => "_eq_job_type",
		"jobTypeIn" => "_in_job_type",
		"jobTypeNotIn" => "_notin_job_type",
		"jobSubTypeEqual" => "_eq_job_sub_type",
		"jobSubTypeIn" => "_in_job_sub_type",
		"jobSubTypeNotIn" => "_notin_job_sub_type",
		"statusEqual" => "_eq_status",
		"statusIn" => "_in_status",
		"statusNotIn" => "_notin_status",
		"priorityGreaterThanOrEqual" => "_gte_priority",
		"priorityLessThanOrEqual" => "_lte_priority",
		"priorityEqual" => "_eq_priority",
		"priorityIn" => "_in_priority",
		"priorityNotIn" => "_notin_priority",
		"batchVersionGreaterThanOrEqual" => "_gte_batch_version",
		"batchVersionLessThanOrEqual" => "_lte_batch_version",
		"batchVersionEqual" => "_eq_batch_version",
		"queueTimeGreaterThanOrEqual" => "_gte_queue_time",
		"queueTimeLessThanOrEqual" => "_lte_queue_time",
		"finishTimeGreaterThanOrEqual" => "_gte_finish_time",
		"finishTimeLessThanOrEqual" => "_lte_finish_time",
		"errTypeEqual" => "_eq_err_type",
		"errTypeIn" => "_in_err_type",
		"errTypeNotIn" => "_notin_err_type",
		"errNumberEqual" => "_eq_err_number",
		"errNumberIn" => "_in_err_number",
		"errNumberNotIn" => "_notin_err_number",
		"estimatedEffortLessThan" => "_lt_estimated_effort",
		"estimatedEffortGreaterThan" => "_gt_estimated_effort",
		"urgencyLessThanOrEqual" => "_lte_urgency",
		"urgencyGreaterThanOrEqual" => "_gte_urgency",
	);

	static private $order_by_map = array
	(
		"+createdAt" => "+created_at",
		"-createdAt" => "-created_at",
		"+updatedAt" => "+updated_at",
		"-updatedAt" => "-updated_at",
		"+executionAttempts" => "+execution_attempts",
		"-executionAttempts" => "-execution_attempts",
		"+lockVersion" => "+lock_version",
		"-lockVersion" => "-lock_version",
		"+status" => "+status",
		"-status" => "-status",
		"+priority" => "+priority",
		"-priority" => "-priority",
		"+queueTime" => "+queue_time",
		"-queueTime" => "-queue_time",
		"+finishTime" => "+finish_time",
		"-finishTime" => "-finish_time",
		"+estimatedEffort" => "+estimated_effort",
		"-estimatedEffort" => "-estimated_effort",
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
	 * @var int
	 */
	public $idGreaterThanOrEqual;

	/**
	 * @var int
	 */
	public $partnerIdEqual;

	/**
	 * @var string
	 */
	public $partnerIdIn;

	/**
	 * @var string
	 */
	public $partnerIdNotIn;

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
	 * @var int
	 */
	public $executionAttemptsGreaterThanOrEqual;

	/**
	 * @var int
	 */
	public $executionAttemptsLessThanOrEqual;

	/**
	 * @var int
	 */
	public $lockVersionGreaterThanOrEqual;

	/**
	 * @var int
	 */
	public $lockVersionLessThanOrEqual;

	/**
	 * @var string
	 */
	public $entryIdEqual;

	/**
	 * @var KalturaBatchJobType
	 */
	public $jobTypeEqual;

	/**
	 * @dynamicType KalturaBatchJobType
	 * @var string
	 */
	public $jobTypeIn;

	/**
	 * @dynamicType KalturaBatchJobType
	 * @var string
	 */
	public $jobTypeNotIn;

	/**
	 * @var int
	 */
	public $jobSubTypeEqual;

	/**
	 * @var string
	 */
	public $jobSubTypeIn;

	/**
	 * @var string
	 */
	public $jobSubTypeNotIn;

	/**
	 * @var KalturaBatchJobStatus
	 */
	public $statusEqual;

	/**
	 * @var string
	 */
	public $statusIn;

	/**
	 * @var string
	 */
	public $statusNotIn;

	/**
	 * @var int
	 */
	public $priorityGreaterThanOrEqual;

	/**
	 * @var int
	 */
	public $priorityLessThanOrEqual;

	/**
	 * @var int
	 */
	public $priorityEqual;

	/**
	 * @var string
	 */
	public $priorityIn;

	/**
	 * @var string
	 */
	public $priorityNotIn;

	/**
	 * @var int
	 */
	public $batchVersionGreaterThanOrEqual;

	/**
	 * @var int
	 */
	public $batchVersionLessThanOrEqual;

	/**
	 * @var int
	 */
	public $batchVersionEqual;

	/**
	 * @var int
	 */
	public $queueTimeGreaterThanOrEqual;

	/**
	 * @var int
	 */
	public $queueTimeLessThanOrEqual;

	/**
	 * @var int
	 */
	public $finishTimeGreaterThanOrEqual;

	/**
	 * @var int
	 */
	public $finishTimeLessThanOrEqual;

	/**
	 * @var KalturaBatchJobErrorTypes
	 */
	public $errTypeEqual;

	/**
	 * @var string
	 */
	public $errTypeIn;

	/**
	 * @var string
	 */
	public $errTypeNotIn;

	/**
	 * @var int
	 */
	public $errNumberEqual;

	/**
	 * @var string
	 */
	public $errNumberIn;

	/**
	 * @var string
	 */
	public $errNumberNotIn;

	/**
	 * @var int
	 */
	public $estimatedEffortLessThan;

	/**
	 * @var int
	 */
	public $estimatedEffortGreaterThan;

	/**
	 * @var int
	 */
	public $urgencyLessThanOrEqual;

	/**
	 * @var int
	 */
	public $urgencyGreaterThanOrEqual;
}
