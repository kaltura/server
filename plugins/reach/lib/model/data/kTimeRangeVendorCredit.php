
<?php

/**
 * Define vendor profile usage credit
 *
 * @package plugins.reach
 * @subpackage model
 *
 */
class kTimeRangeVendorCredit extends kVendorCredit
{
	/**
	 *  @var string
	 */
	protected $toDate;
	
	/**
	 * @return the $toDate
	 */
	public function getToDate()
	{
		return $this->toDate;
	}
	
	/**
	 * @param string $toDate
	 */
	public function setToDate($toDate)
	{
		$endOfDay = kReachUtils::reachStrToTime("tomorrow", $toDate) - 1;
		$this->toDate = $endOfDay;
	}

	public function addAdditionalCriteria(Criteria $c)
	{
		$c->addAnd(EntryVendorTaskPeer::QUEUE_TIME ,$this->getSyncCreditToDate() , Criteria::LESS_EQUAL);
	}

	/**
	 * @param bool $includeOverages
	 * @return int
	 */
	public function getCurrentCredit($includeOverages = true)
	{
		$now = time();
		if ( $now < $this->fromDate || $now > $this->toDate )
		{
			KalturaLog::debug("Current date [$now] is not in credit time range  [from - $this->fromDate , to - $this->toDate] ");
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
	 * @return bool
	 */
	public function isActive($time = null)
	{
		$now = $time != null ? $time : time();
		if (!parent::isActive($now))
			return false;

		return !$this->toDateHasExpired($now);
	}

	public function toDateHasExpired($now)
	{
		if ( $now > $this->toDate)
		{
			KalturaLog::debug("Current date [$now] is not in credit time Range [from - $this->fromDate to - $this->toDate] ");
			return true;
		}
		return false;
	}
	
	public function getSyncCreditToDate()
	{
		return $this->toDate;
	}

	public function shouldResetLastCreditExpiry($lastCreditExpiry)
	{
		if(!$lastCreditExpiry)
			return false;

		if($this->getToDate() > $lastCreditExpiry && $this->getToDate() > time())
			return true;

		return false;
	}
}
