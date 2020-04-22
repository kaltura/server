
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
	 * @param bool $includeOverages
	 * @return int
	 */
	public function getCurrentCredit($includeOverages = true)
	{
		return $this->credit;
	}

	public function isActive($time = null)
	{
		$now = $time != null ? $time : time();
		if (!parent::isActive($now))
			return false;

		return !$this->toDateHasExpired($now);
	}

}
