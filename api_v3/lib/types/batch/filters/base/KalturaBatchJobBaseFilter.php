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
		"jobSubTypeNotIn" => "_notin_job_sub_type",
		"onStressDivertToEqual" => "_eq_on_stress_divert_to",
		"onStressDivertToIn" => "_in_on_stress_divert_to",
		"onStressDivertToNotIn" => "_notin_on_stress_divert_to",
		"statusEqual" => "_eq_status",
		"statusIn" => "_in_status",
		"statusNotIn" => "_notin_status",
		"abortEqual" => "_eq_abort",
		"checkAgainTimeoutGreaterThanOrEqual" => "_gte_check_again_timeout",
		"checkAgainTimeoutLessThanOrEqual" => "_lte_check_again_timeout",
		"progressGreaterThanOrEqual" => "_gte_progress",
		"progressLessThanOrEqual" => "_lte_progress",
		"updatesCountGreaterThanOrEqual" => "_gte_updates_count",
		"updatesCountLessThanOrEqual" => "_lte_updates_count",
		"priorityGreaterThanOrEqual" => "_gte_priority",
		"priorityLessThanOrEqual" => "_lte_priority",
		"priorityEqual" => "_eq_priority",
		"priorityIn" => "_in_priority",
		"priorityNotIn" => "_notin_priority",
		"twinJobIdEqual" => "_eq_twin_job_id",
		"twinJobIdIn" => "_in_twin_job_id",
		"twinJobIdNotIn" => "_notin_twin_job_id",
		"bulkJobIdEqual" => "_eq_bulk_job_id",
		"bulkJobIdIn" => "_in_bulk_job_id",
		"bulkJobIdNotIn" => "_notin_bulk_job_id",
		"parentJobIdEqual" => "_eq_parent_job_id",
		"parentJobIdIn" => "_in_parent_job_id",
		"parentJobIdNotIn" => "_notin_parent_job_id",
		"rootJobIdEqual" => "_eq_root_job_id",
		"rootJobIdIn" => "_in_root_job_id",
		"rootJobIdNotIn" => "_notin_root_job_id",
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
		"fileSizeLessThan" => "_lt_file_size",
		"fileSizeGreaterThan" => "_gt_file_size",
		"lastWorkerRemoteEqual" => "_eq_last_worker_remote",
		"schedulerIdEqual" => "_eq_scheduler_id",
		"schedulerIdIn" => "_in_scheduler_id",
		"schedulerIdNotIn" => "_notin_scheduler_id",
		"workerIdEqual" => "_eq_worker_id",
		"workerIdIn" => "_in_worker_id",
		"workerIdNotIn" => "_notin_worker_id",
		"batchIndexEqual" => "_eq_batch_index",
		"batchIndexIn" => "_in_batch_index",
		"batchIndexNotIn" => "_notin_batch_index",
		"lastSchedulerIdEqual" => "_eq_last_scheduler_id",
		"lastSchedulerIdIn" => "_in_last_scheduler_id",
		"lastSchedulerIdNotIn" => "_notin_last_scheduler_id",
		"lastWorkerIdEqual" => "_eq_last_worker_id",
		"lastWorkerIdIn" => "_in_last_worker_id",
		"lastWorkerIdNotIn" => "_notin_last_worker_id",
		"dcEqual" => "_eq_dc",
		"dcIn" => "_in_dc",
		"dcNotIn" => "_notin_dc",
	);

	private $order_by_map = array
	(
		"+status" => "+status",
		"-status" => "-status",
		"+checkAgainTimeout" => "+check_again_timeout",
		"-checkAgainTimeout" => "-check_again_timeout",
		"+progress" => "+progress",
		"-progress" => "-progress",
		"+updatesCount" => "+updates_count",
		"-updatesCount" => "-updates_count",
		"+priority" => "+priority",
		"-priority" => "-priority",
		"+queueTime" => "+queue_time",
		"-queueTime" => "-queue_time",
		"+finishTime" => "+finish_time",
		"-finishTime" => "-finish_time",
		"+fileSize" => "+file_size",
		"-fileSize" => "-file_size",
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
	public $jobSubTypeNotIn;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $onStressDivertToEqual;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $onStressDivertToIn;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $onStressDivertToNotIn;

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
	 * @var string
	 */
	public $statusNotIn;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $abortEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $checkAgainTimeoutGreaterThanOrEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $checkAgainTimeoutLessThanOrEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $progressGreaterThanOrEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $progressLessThanOrEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $updatesCountGreaterThanOrEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $updatesCountLessThanOrEqual;

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
	public $priorityEqual;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $priorityIn;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $priorityNotIn;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $twinJobIdEqual;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $twinJobIdIn;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $twinJobIdNotIn;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $bulkJobIdEqual;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $bulkJobIdIn;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $bulkJobIdNotIn;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $parentJobIdEqual;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $parentJobIdIn;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $parentJobIdNotIn;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $rootJobIdEqual;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $rootJobIdIn;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $rootJobIdNotIn;

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
	 * @var KalturaBatchJobErrorTypes
	 */
	public $errTypeEqual;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $errTypeIn;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $errTypeNotIn;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $errNumberEqual;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $errNumberIn;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $errNumberNotIn;

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

	/**
	 * 
	 * 
	 * @var bool
	 */
	public $lastWorkerRemoteEqual;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $schedulerIdEqual;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $schedulerIdIn;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $schedulerIdNotIn;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $workerIdEqual;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $workerIdIn;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $workerIdNotIn;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $batchIndexEqual;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $batchIndexIn;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $batchIndexNotIn;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $lastSchedulerIdEqual;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $lastSchedulerIdIn;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $lastSchedulerIdNotIn;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $lastWorkerIdEqual;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $lastWorkerIdIn;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $lastWorkerIdNotIn;

	/**
	 * 
	 * 
	 * @var int
	 */
	public $dcEqual;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $dcIn;

	/**
	 * 
	 * 
	 * @var string
	 */
	public $dcNotIn;
}
