
<?php

/**
 * Define vendor profile usage unlimited credit
 *
 * @package plugins.reach
 * @subpackage model
 *
 */
class kUnlimitedVendorCredit extends kVendorCredit
{
	/**
	 * @var int
	 * @readonly
	 */
	protected $credit = ReachProfileCreditValues::UNLIMITED_CREDIT;
	
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
	
	/***
	 * @param $date
	 * @return int
	 */
	public function getCurrentCredit($includeOverages = true)
	{
		$now = time();
		if ( $now < $this->fromDate || ($this->toDate && $now > $this->toDate) )
		{
			KalturaLog::debug("Current date [$now] is not in credit time Range [ from - $this->fromDate to - $this->toDate] ");
			return 0;
		}
		
		return $this->credit;
	}

}