<?php
/**
 * @package api
 * @subpackage filters.base
 * @abstract
 */
class KalturaBatchJobBaseFilter extends KalturaBaseJobFilter
{
	private $map_between_objects = array
	(
		"entryIdEqual" => "_eq_entry_id",
		"jobTypeEqual" => "_eq_job_type",
		"jobTypeIn" => "_in_job_type",
		"jobTypeNotIn" => "_notin_job_type",
		"jobSubTypeEqual" => "_eq_job_sub_type",
		"jobSubTypeIn" => "_in_job_sub_type",
		"onStressDivertToIn" => "_in_on_stress_divert_to",
		"statusEqual" => "_eq_status",
		"statusIn" => "_in_status",
		"priorityGreaterThanOrEqual" => "_gte_priority",
		"priorityLessThanOrEqual" => "_lte_priority",
		"queueTimeGreaterThanOrEqual" => "_gte_queue_time",
		"queueTimeLessThanOrEqual" => "_lte_queue_time",
		"finishTimeGreaterThanOrEqual" => "_gte_finish_time",
		"finishTimeLessThanOrEqual" => "_lte_finish_time",
		"errTypeIn" => "_in_err_type",
		"fileSizeLessThan" => "_lt_file_size",
		"fileSizeGreaterThan" => "_gt_file_size",
	);

	private $order_by_map = array
	(
		"+status" => "+status",
		"-status" => "-status",
		"+queueTime" => "+queue_time",
		"-queueTime" => "-queue_time",
		"+finishTime" => "+finish_time",
		"-finishTime" => "-finish_time",
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
	 * @var string
	 */
	public $entryIdEqual;

	/**
	 * 
	 * 
	 * @var KalturaBatchJobType
	 */
	public $jobTypeEqual;

	/**
	 * 
	 * 
	 * @dynamicType KalturaBatchJobType
	 * @var string
	 */
	public $jobTypeIn;

	/**
	 * 
	 * 
	 * @dynamicType KalturaBatchJobType
	 * @var string
	 */
	public $jobTypeNotIn;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $jobSubTypeEqual;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $jobSubTypeIn;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $onStressDivertToIn;

	/**
	 * 
	 * 
	 * @var KalturaBatchJobStatus
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
	public $priorityGreaterThanOrEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $priorityLessThanOrEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $queueTimeGreaterThanOrEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $queueTimeLessThanOrEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $finishTimeGreaterThanOrEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $finishTimeLessThanOrEqual;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $errTypeIn;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $fileSizeLessThan;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $fileSizeGreaterThan;
}
