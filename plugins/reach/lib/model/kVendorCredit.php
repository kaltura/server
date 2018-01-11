
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
	 *  @var int
	 */
	protected $credit;
	
	/**
	 *  @var string
	 */
	protected $fromDate;
	
	/**
	 *  @var bool
	 */
	protected $allowOverage;
	
	/**
	 *  @var int
	 */
	protected $overageCredit;
	
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
}