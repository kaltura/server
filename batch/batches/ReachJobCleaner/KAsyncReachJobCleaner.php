<?php
/**
 * Going over all the entry vendor task items with:
 * service type = HUMAN
 * status pending OR processing
 * if the due date passed for more than overdueTimePercentage
 * update status to Error with description: Job Due Date was exceeded in $overdueTimePercentage %
 *
 * @package Scheduler
 * @subpackage ReachJobCleaner
 */
require_once('/opt/kaltura/app//alpha/lib/interfaces/IBaseObject.php');
require_once('/opt/kaltura/app/alpha/lib/interfaces/IIndexable.php');
require_once('/opt/kaltura/app/alpha/lib/interfaces/IRelatedObject.php');

class KAsyncReachJobCleaner extends KPeriodicWorker
{
	const JOB_DUE_DATE_EXCEEDED_MESSAGE = 'Job Due Date was exceeded in ';
	const MAX_PAGE_SIZE = 500;
	const MAX_MATCHES = 10000;

	/*
	* @var KalturaReachClientPlugin
	*/
	private $reachClientPlugin = null;

	private $overdueTimePercentage = 100;

	/**
	 * @param KSchedularTaskConfig $taskConfig
	 */
	public function __construct($taskConfig = null)
	{
		parent::__construct($taskConfig);
		$this->reachClientPlugin = KalturaReachClientPlugin::get(self::$kClient);
		$overdueTimePercentage = $this->getAdditionalParams('overdueTimePercentage');
		$this->overdueTimePercentage = $overdueTimePercentage ? $overdueTimePercentage : 100;
	}

	/* (non-PHPdoc)
	 * @see KBatchBase::getType()
	 */
	public static function getType()
	{
		return KalturaBatchJobType::REACH_JOB_CLEANER;
	}

	/* (non-PHPdoc)
	 * @see KBatchBase::run()
	*/
	public function run($jobs = null)
	{
		KalturaLog::debug('Getting all the entry vendor task items');
		$filter = new KalturaEntryVendorTaskFilter();
		$filter->statusIn = KalturaEntryVendorTaskStatus::PENDING . ',' . KalturaEntryVendorTaskStatus::PROCESSING;
		$filter->expectedFinishTimeLessThanOrEqual = time();
		$filter->updatedAtGreaterThanOrEqual = time() -  (VendorServiceTurnAroundTime::TEN_DAYS + VendorServiceTurnAroundTime::FOUR_DAYS);
		$filter->orderBy = KalturaEntryVendorTaskOrderBy::UPDATED_AT_ASC;

		$pager = new KalturaFilterPager();
		$pager->pageSize = self::MAX_PAGE_SIZE;
		$pager->pageIndex = 1;

		try
		{
			$entryVendorTaskList = $this->reachClientPlugin->entryVendorTask->listAction($filter, $pager);
		}
		catch (Exception $e)
		{
			KalturaLog::debug('Could not list Entry Vendor Tasks ' . $e->getMessage());
			return;
		}

		$totalCount = min($entryVendorTaskList->totalCount, self::MAX_MATCHES - 1);
		while ($totalCount > 0 && $pager->pageIndex <= 20)
		{
			$this->updateStuckJobs($entryVendorTaskList);

			$pager->pageIndex++;
			$totalCount = $totalCount - $pager->pageSize;
			$pager->pageSize = min(self::MAX_PAGE_SIZE, $totalCount);
			if ($pager->pageSize > 0)
			{
				try
				{
					$entryVendorTaskList = $this->reachClientPlugin->entryVendorTask->listAction($filter, $pager);
				}
				catch (Exception $e)
				{
					KalturaLog::debug('Could not list Entry Vendor Tasks ' . $e->getMessage());
					return;
				}
			}
		}

		KalturaLog::debug('Done');
	}

	protected function updateStuckJobs($entryVendorTaskList)
	{
		foreach ($entryVendorTaskList->objects as $entryVendorTask)
		{
			if ($entryVendorTask->serviceType ==  KalturaVendorServiceType::HUMAN &&
				$this->isDueDatePassed($entryVendorTask))
			{
				KalturaLog::debug('Going to update EntryVendorTask id ' . $entryVendorTask->id . ' to status ERROR. '
					. self::JOB_DUE_DATE_EXCEEDED_MESSAGE . $this->overdueTimePercentage . '%');

				$kEntryVendorTask = new KalturaEntryVendorTask();
				$kEntryVendorTask->status = KalturaEntryVendorTaskStatus::ERROR;
				$kEntryVendorTask->errDescription = self::JOB_DUE_DATE_EXCEEDED_MESSAGE . $this->overdueTimePercentage . '%';
				try
				{
					$this->reachClientPlugin->entryVendorTask->update($entryVendorTask->id, $kEntryVendorTask);
				}
				catch (Exception $e)
				{
					KalturaLog::debug("Couldn't update entry Vendor Task id: [$entryVendorTask->id] " . $e->getMessage());
					continue;
				}
			}
		}
	}

	protected function isDueDatePassed($entryVendorTask)
	{
		$dueDate = $entryVendorTask->expectedFinishTime;
		$turnAroundTime = $entryVendorTask->turnAroundTime;

		if ($dueDate && $turnAroundTime)
		{
			if ($turnAroundTime == VendorServiceTurnAroundTime::BEST_EFFORT)
			{
				$turnAroundTime = EntryVendorTask::SEVEN_DAYS;
			}
			elseif( (VendorServiceTurnAroundTime::ONE_BUSINESS_DAY <= $turnAroundTime) &&
				($turnAroundTime <= VendorServiceTurnAroundTime::SEVEN_BUSINESS_DAYS) )
			{
				$turnAroundTime = EntryVendorTask::calculateBusinessDays($turnAroundTime, time());
			}

			if (time() >= ($dueDate + ($turnAroundTime * ($this->overdueTimePercentage / 100))) )
			{
				return true;
			}
		}

		return false;
	}
}