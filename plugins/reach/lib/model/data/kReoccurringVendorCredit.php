<?php

/**
 * Define vendor profile usage credit
 *
 * @package plugins.reach
 * @subpackage model
 *
 */
class kReoccurringVendorCredit extends kTimeRangeVendorCredit
{
	/**
	 * @var VendorCreditRecurrenceFrequency
	 */
	protected $frequency;

	/**
	 * @var string
	 */
	protected $periodStartDate;

	/**
	 * @var string
	 */
	protected $periodEndDate;

	/**
	 * @var int
	 */
	protected $initialOverageCredit;

	/**
	 * @var bool
	 */
	protected $initialOverageCreditInitialized;

	/**
	 * @param string $toDate
	 */
	public function setFromDate($toDate)
	{
		parent::setFromDate($toDate);
		$this->periodStartDate = $this->fromDate;

	}

	/**
	 * @return string $frequency
	 */
	public function getFrequency()
	{
		return $this->frequency;
	}

	/**
	 * @param ScheduleEventRecurrenceFrequency $frequency
	 */
	public function setFrequency($frequency)
	{
		$this->frequency = $frequency;
	}

	/**
	 * @param ReachProfile $reachProfile
	 * @return int
	 * @throws PropelException
	 */
	public function syncCredit(ReachProfile $reachProfile)
	{
		$syncedCredit = parent::syncCredit($reachProfile);
		if ($reachProfile->getLastSyncTime() > $this->periodEndDate)
		{
			$syncedCredit = 0;
			$this->calculateNextPeriodDates( $this->periodEndDate, $reachProfile->getLastSyncTime());
			$this->overageCredit = $this->initialOverageCredit;
			$reachProfile->setSyncedCredit($syncedCredit);
			$reachProfile->setAddOn(0);
		}
		return $syncedCredit;
	}

	/**
	 * @param $startTime
	 * @param $currentDate
	 */
	public function calculateNextPeriodDates($startTime, $currentDate)
	{
		$endTime = kReachUtils::reachStrToTime('+1 ' . $this->getFrequency(), $startTime);
		while ($endTime < $currentDate)
		{
			$startTime = $endTime;
			$endTime = kReachUtils::reachStrToTime('+1 ' . $this->getFrequency(), $endTime);
		}
		$this->periodStartDate = kReachUtils::reachStrToTime("tomorrow" , $startTime);
		$this->periodEndDate = min($endTime, $this->getToDate());
		$this->periodEndDate = kReachUtils::reachStrToTime("tomorrow", $this->periodEndDate)-1;
	}

	/**
	 * @param $startTime
	 * @param $currentDate
	 */
	public function initiatePeriodDates($startTime, $currentDate)
	{
		$endTime = kReachUtils::reachStrToTime('+1 ' . $this->getFrequency(), $startTime);
		while ($endTime < $currentDate)
		{
			$startTime = $endTime;
			$endTime = kReachUtils::reachStrToTime('+1 ' . $this->getFrequency(), $endTime);
		}
		$this->periodStartDate = kReachUtils::reachStrToTime("today", $startTime);
		$this->periodEndDate = min($endTime, $this->getToDate());
		$this->periodEndDate = kReachUtils::reachStrToTime("today", $this->periodEndDate) -1;
	}

	public function setPeriodDates()
	{
		$this->periodStartDate = $this->getFromDate();
		$this->periodEndDate = $this->getFromDate();
		$this->initiatePeriodDates($this->periodEndDate, time());
	}

	/**
	 * @param int $addOn
	 * @param bool $includeOverages
	 * @return int
	 */
	public function getCurrentCredit($addOn, $includeOverages = true)
	{
		$now = time();
		if ($now < $this->periodStartDate || $now > $this->periodEndDate)
		{
			KalturaLog::debug("Current date [$now] is not in credit time range  [from - $this->periodStartDate , to - $this->periodEndDate] ");
			return 0;
		}

		$credit = $this->credit;
		if ($includeOverages && $this->overageCredit)
		{
			$credit += $this->overageCredit;
		}

		if($addOn)
		{
			$credit += $addOn;
		}

		return $credit;
	}

	/**
	 * @param null $time
	 * @return bool
	 */
	public function isActive($time = null)
	{
		$now = $time != null ? $time : time();
		if (!parent::isActive($now))
		{
			return false;
		}

		if ($now < $this->periodStartDate || $now > $this->periodEndDate)
		{
			KalturaLog::debug("Current date [$now] is not in frequency credit time Range cycle [from - $this->periodStartDate to - $this->periodEndDate] ");
			return false;
		}
		return true;
	}

	/**
	 * @param int $overageCredit
	 */
	public function setOverageCredit($overageCredit)
	{
		$this->overageCredit = $overageCredit;
		if (!$this->initialOverageCreditInitialized)
		{
			$this->initialOverageCredit = $overageCredit;
			$this->initialOverageCreditInitialized = true;
		}
	}

	/**
	 * @return string
	 */
	public function getSyncCreditToDate()
	{
		return $this->periodEndDate;
	}

	/**
	 * @param $lastSyncTime
	 * @return string|the
	 */
	public function getSyncCreditStartDate($lastSyncTime)
	{
		if(!$lastSyncTime && !$this->periodStartDate)
			return $this->getFromDate();
		
		if(!$lastSyncTime && $this->periodStartDate)
			return $this->periodStartDate;
		
		if($lastSyncTime && $this->periodStartDate && $lastSyncTime < $this->periodStartDate)
			return $this->periodStartDate;
		
		return parent::getSyncCreditStartDate($lastSyncTime);
	}
}
