
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
	 * @return int
	 */
	public function getCredit()
	{
		return $this->credit;
	}

	/**
	 * @return string
	 */
	public function getFromDate()
	{
		return $this->fromDate;
	}

	/**
	 * @return int
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
	 * @return int
	 */
	public function getAddOn()
	{
		return $this->addOn;
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
	 * @return int
	 */
	public function getSyncedCredit()
	{
		return $this->syncedCredit ? $this->syncedCredit : 0;
	}

	/**
	 * @return string $lastSyncTime
	 */
	public function getLastSyncTime()
	{
		return $this->lastSyncTime;
	}

	public function addAdditionalCriteria(Criteria $c)
	{
	}

	/**
	 * @param ReachProfile $reachProfile
	 * @return int
	 * @throws PropelException
	 */
	public function syncCredit(ReachProfile $reachProfile)
	{
		$c = new Criteria();
		$c->add(EntryVendorTaskPeer::REACH_PROFILE_ID, $reachProfile->getId() , Criteria::EQUAL);
		$c->add(EntryVendorTaskPeer::STATUS, array(EntryVendorTaskStatus::PENDING, EntryVendorTaskStatus::PROCESSING, EntryVendorTaskStatus::READY), Criteria::IN);
		$c->add(EntryVendorTaskPeer::QUEUE_TIME, $this->getSyncCreditStartDate($reachProfile->getLastSyncTime()), Criteria::GREATER_EQUAL);
		$c->add(EntryVendorTaskPeer::PRICE, 0, Criteria::NOT_EQUAL);
		$c->add(EntryVendorTaskPeer::PARTNER_ID, $reachProfile->getPartnerId());
		$c->addSelectColumn('SUM('. EntryVendorTaskPeer::PRICE .')');
		$this->addAdditionalCriteria($c);

		$now = time();
		$stmt = EntryVendorTaskPeer::doSelectStmt($c);
		$row = $stmt->fetch(PDO::FETCH_NUM);

		$totalUsedCredit = $reachProfile->getSyncedCredit();

		$totalPrice = $row[0];
		if($totalPrice)
		{
			$totalUsedCredit += $totalPrice;
		}

		$reachProfile->setSyncedCredit($totalUsedCredit);
		$reachProfile->setLastSyncTime($now);

		return $totalUsedCredit;
	}

	/***
	 * @param int $addOn
	 * @param bool $includeOverages should return current credit including overageCredit info or not (Default is true)
	 * @return int
	 */
	public function getCurrentCredit($addOn, $includeOverages = true)
	{
		$now = time();
		if ( $now < $this->fromDate)
		{
				KalturaLog::debug("Current date [$now] is not in credit time Range [ from - $this->fromDate ] ");
				return 0;
		}
		
		$credit = $this->credit;
		if($includeOverages && $this->overageCredit)
			$credit += $this->overageCredit;

		if($addOn)
		{
			$credit += $addOn;
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

	/**
	 * @param $lastSyncTime
	 * @return the
	 */
	public function getSyncCreditStartDate($lastSyncTime)
	{
		return $lastSyncTime ? $lastSyncTime : $this->getFromDate();
	}

	/**
	 * @param $lastCreditExpiry
	 * @return bool
	 */
	public function shouldResetLastCreditExpiry($lastCreditExpiry)
	{
		return false;
	}

}
