
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
		$this->toDate = $toDate;
	}

	public function addAdditionalCriteria(Criteria $c)
	{
		$c->add(EntryVendorTaskPeer::QUEUE_TIME ,$this->getToDate() , Criteria::LESS_EQUAL);
	}

	/***
	 * @param $date
	 * @return int
	 */
	public function getCurrentCredit()
	{
		$now = time();
		if ( $now < $this->fromDate || $now > $this->toDate )
		{
			KalturaLog::debug("Current date [$now] is not in credit time range  [ from - $this->fromDate , to - $this->toDate] ");
			return 0;
		}
		
		$credit = $this->credit;
		if($this->allowOverage)
			$credit += $this->overageCredit;
		
		return $credit;
	}

}