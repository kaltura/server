
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
		$now = time();
		$syncStartDate = $this->getSyncCreditStartDate();

		$totalUsedCredit = $this->getSyncedCredit();
		$totalPrice = 0;

		$c = new Criteria();
		$c->add(EntryVendorTaskPeer::REACH_PROFILE_ID, $reachProfileId);
		$c->add(EntryVendorTaskPeer::PARTNER_ID, $partnerId);
		$c->add(EntryVendorTaskPeer::PRICE, 0, Criteria::NOT_EQUAL);
		$c->add(
			EntryVendorTaskPeer::STATUS,
			[
				EntryVendorTaskStatus::PENDING,
				EntryVendorTaskStatus::PROCESSING,
				EntryVendorTaskStatus::READY,
			],
			Criteria::IN
		);
		$c->add(
			EntryVendorTaskPeer::QUEUE_TIME,
			$syncStartDate,
			Criteria::GREATER_EQUAL
		);

		$tasks = EntryVendorTaskPeer::doSelect($c);

		foreach ($tasks as $task)
		{
			if ($task->getIsPayPerUse())
			{
				if ($task->getFinishTime() && $task->getFinishTime() >= $syncStartDate)
				{
					$totalPrice += $task->getPrice();
				}
			}
			else
			{
				$totalPrice += $task->getPrice();
			}
		}

		if ($totalPrice)
		{
			$totalUsedCredit += $totalPrice;
		}

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
