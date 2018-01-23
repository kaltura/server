
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

}