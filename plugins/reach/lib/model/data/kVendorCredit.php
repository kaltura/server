
<?php

/**
 * Define vendor profile usage credit
 *
 * @package plugins.reach
 * @subpackage model
 *
 */
class kVendorCredit
{
	/**
	 * @var int
	 */
	protected $credit;

	/**
	 * @var string
	 */
	protected $fromDate;

	/**
	 * @var int
	 */
	protected $overageCredit;

	/**
	 * @var int
	 */
	protected $addOn;

	/**
	 * @var string
	 */
	public $lastSyncTime;

	/**
	 * @var int
	 */
	public $syncedCredit;

	/**
	 * @return the $credit
	 */
	public function getCredit()
	{
		return $this->credit;
	}

	/**
	 * @return the $fromDate
	 */
	public function getFromDate()
	{
		return $this->fromDate;
	}

	/**
	 * @return the $overageCredit
	 */
	public function getOverageCredit()
	{
		return $this->overageCredit;
	}


	/**
	 * @param int $credit
	 */
	public function setCredit($credit)
	{
		$this->credit = $credit;
	}

	/**
	 * @return the $addOn
	 */
	public function getAddOn()
	{
		return $this->addOn;
	}

	/**
	 * @param int $addOn
	 */
	public function setAddOn($addOn)
	{
		$this->addOn = $addOn;
	}

	/**
	 * @param string $fromDate
	 */
	public function setFromDate($fromDate)
	{
		$beginOfDay = kReachUtils::reachStrToTime("today", $fromDate);
		$this->fromDate = $beginOfDay;
	}

	/**
	 * @param int $overageCredit
	 */
	public function setOverageCredit($overageCredit)
	{
		$this->overageCredit = $overageCredit;
	}

	/**
	 * @return the $credit
	 */
	public function getSyncedCredit()
	{
		return $this->syncedCredit ? $this->syncedCredit : 0;
	}

	/**
	 * @param int $SyncedCredit
	 */
	public function setSyncedCredit($SyncedCredit)
	{
		$this->syncedCredit = $SyncedCredit;
	}


	/**
	 * @return string $lastSyncTime
	 */
	public function getLastSyncTime()
	{
		return $this->lastSyncTime;
	}

	/**
	 * @param string $lastSyncTime
	 */
	public function setLastSyncTime($lastSyncTime)
	{
		$this->lastSyncTime = $lastSyncTime;
	}

	public function addAdditionalCriteria(Criteria $c)
	{
	}

	public function isSynced()
	{
		return (intval(time() / 86400) == (intval($this->lastSyncTime / 86400)));
	}

	public function syncCredit($reachProfileId, $partnerId)
	{
		// Get the lookback window for PPU tasks (default 7 days)
		// This ensures we catch PPU tasks that were queued before lastSync but finished after
		$lookbackWindow = kConf::get('reach_sync_credit_lookback_window', 'local', 604800); // 7 days in seconds
		$syncStartDate = $this->getSyncCreditStartDate();
		$lookbackStartDate = $syncStartDate - $lookbackWindow;

		KalturaLog::info("Starting credit sync for reach profile [$reachProfileId] partner [$partnerId] with sync start date [$syncStartDate] and lookback start date [$lookbackStartDate]");

		// Single query with lookback window to fetch all tasks (PPU and non-PPU)
		// Uses indexed reach_profile_queue_time composite index for optimal performance
		$c = new Criteria();
		$c->add(EntryVendorTaskPeer::REACH_PROFILE_ID, $reachProfileId, Criteria::EQUAL);
		$c->add(EntryVendorTaskPeer::STATUS, array(EntryVendorTaskStatus::PENDING, EntryVendorTaskStatus::PROCESSING, EntryVendorTaskStatus::READY), Criteria::IN);
		$c->add(EntryVendorTaskPeer::PRICE, 0, Criteria::NOT_EQUAL);
		$c->add(EntryVendorTaskPeer::QUEUE_TIME, $lookbackStartDate, Criteria::GREATER_EQUAL);
		$c->add(EntryVendorTaskPeer::PARTNER_ID, $partnerId);
		$this->addAdditionalCriteria($c);

		$now = time();
		$tasks = EntryVendorTaskPeer::doSelect($c);

		$totalUsedCredit = $this->getSyncedCredit();
		$ppuTasksCount = 0;
		$nonPpuTasksCount = 0;
		$ppuCredit = 0;
		$nonPpuCredit = 0;

		// Iterate through tasks and filter by isPayPerUse flag in PHP
		foreach ($tasks as $task)
		{
			/** @var EntryVendorTask $task */
			$taskPrice = $task->getPrice();

			// Check if this is a pay-per-use task using the flag
			$isPayPerUse = $task->getIsPayPerUse();

			if ($isPayPerUse)
			{
				// PPU tasks: count only if status is READY and finish_time >= syncStartDate
				// This ensures we only count PPU tasks that were actually completed and priced after last sync
				if ($task->getStatus() == EntryVendorTaskStatus::READY && $task->getFinishTime() >= $syncStartDate)
				{
					$totalUsedCredit += $taskPrice;
					$ppuCredit += $taskPrice;
					$ppuTasksCount++;
				}
			}
			else
			{
				// Non-PPU tasks: count if queue_time >= syncStartDate
				// These tasks are charged immediately upon creation
				if ($task->getQueueTime() >= $syncStartDate)
				{
					$totalUsedCredit += $taskPrice;
					$nonPpuCredit += $taskPrice;
					$nonPpuTasksCount++;
				}
			}
		}

		KalturaLog::info("Credit sync completed: PPU tasks [$ppuTasksCount] with credit [$ppuCredit], Non-PPU tasks [$nonPpuTasksCount] with credit [$nonPpuCredit], Total credit [$totalUsedCredit]");

		$this->setSyncedCredit($totalUsedCredit);
		$this->setLastSyncTime($now);

		return $totalUsedCredit;
	}

	/***
	 * @param $includeOverages should return current credit including overageCredit info or not (Default is true)
	 * @return int
	 */
	public function getCurrentCredit($includeOverages = true, $validateActive = true)
	{
		$now = time();
		if ($validateActive && $now < $this->fromDate)
		{
				KalturaLog::debug("Current date [$now] is not in credit time Range [ from - $this->fromDate ] ");
				return 0;
		}
		
		$credit = $this->credit;
		if($includeOverages && $this->overageCredit)
		{
			$credit += $this->overageCredit;
		}

		if($this->addOn)
		{
			$credit += $this->addOn;
		}

		return $credit;
	}

	/***
	 * @param $time
	 * @return bool
	 */
	public function isActive($time = null)
	{
		$now = $time != null ? $time : time();
		if ( $now < $this->fromDate)
		{
			KalturaLog::debug("Current date [$now] is not in credit time Range [ from - $this->fromDate ] ");
			return false;
		}
		return true;
	}

	public function toDateHasExpired($now)
	{
		return false;
	}
	
	public function getSyncCreditStartDate()
	{
		return $this->getLastSyncTime() ? $this->getLastSyncTime() : $this->getFromDate();
	}

	public function shouldResetLastCreditExpiry($lastCreditExpiry)
	{
		return false;
	}

	/**
	 * @param $sourceCredit
	 */
	public function setInnerParams($sourceCredit)
	{
		if ($sourceCredit)
		{
			/** @var kVendorCredit $sourceCredit */
			$this->lastSyncTime = $sourceCredit->getLastSyncTime();
			$this->syncedCredit = $sourceCredit->getSyncedCredit();
		}
	}
}
