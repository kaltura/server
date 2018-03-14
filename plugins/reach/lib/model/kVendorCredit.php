
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
	 * @var bool
	 */
	protected $allowOverage;

	/**
	 * @var int
	 */
	protected $overageCredit;

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
	 * @return the $allowOverage
	 */
	public function getAllowOverage()
	{
		return $this->allowOverage;
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
	 * @param string $fromDate
	 */
	public function setFromDate($fromDate)
	{
		$this->fromDate = $fromDate;
	}

	/**
	 * @param bool $allowOverage
	 */
	public function setAllowOverage($allowOverage)
	{
		$this->allowOverage = $allowOverage;
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
		return $this->syncedCredit;
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

	public function syncCredit($vendorProfileId)
	{
		$c = new Criteria();
		$c->add(EntryVendorTaskPeer::VENDOR_PROFILE_ID, $vendorProfileId , Criteria::EQUAL);
		$c->add(EntryVendorTaskPeer::STATUS, array(EntryVendorTaskStatus::PENDING, EntryVendorTaskStatus::PROCESSING, EntryVendorTaskStatus::READY), Criteria::IN);
		$date = $this->getLastSyncTime() ? $this->getLastSyncTime() : $this->getFromDate();
		$c->add(EntryVendorTaskPeer::QUEUE_TIME, $date, Criteria::GREATER_EQUAL);
		$this->addAdditionalCriteria($c);

		$now = time();
		$entryVendorTasks = EntryVendorTaskPeer::doSelect($c);
		$totalUsedCredit = $this->getSyncedCredit();
		foreach ($entryVendorTasks as $entryVendorTask)
		{
			/* @var $entryVendorTask EntryVendorTask */
			$totalUsedCredit += $entryVendorTask->getPrice();
		}
		$this->setSyncedCredit($totalUsedCredit);
		$this->setLastSyncTime($now);

		return $totalUsedCredit;
	}

	/***
	 * @param $includeOverages should return current credit including overageCredit info or not (Default is true)
	 * @return int
	 */
	public function getCurrentCredit($includeOverages = true)
	{
		$now = time();
		if ( $now < $this->fromDate)
		{
				KalturaLog::debug("Current date [$now] is not in credit time Range [ from - $this->fromDate ] ");
				return 0;
		}
		
		$credit = $this->credit;
		if($includeOverages && $this->allowOverage)
			$credit += $this->overageCredit;
		
		return $credit;
	}
}