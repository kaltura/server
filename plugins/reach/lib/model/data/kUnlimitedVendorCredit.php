
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
	
	/***
	 * @param $date
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
		
		return $this->credit;
	}

}