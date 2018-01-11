
<?php

/**
 * Define vendor profile usage credit
 *
 * @package plugins.reach
 * @subpackage model
 *
 */
class kReoccurringVendorCredit extends kVendorCredit
{
	/**
	 *  @var int
	 */
	protected $reOccurrenceCount;
	
	/**
	 *  @var VendorCreditRecurrenceFrequency
	 */
	protected $frequency;
	
	/**
	 * @return the $reOccurrenceCount
	 */
	public function getReOccurrenceCount()
	{
		return $this->reOccurrenceCount;
	}
	
	/**
	 * @return the $frequency
	 */
	public function getFrequency()
	{
		return $this->frequency;
	}
	
	/**
	 * @param string $reOccurrenceCount
	 */
	public function setReOccurrenceCount($reOccurrenceCount)
	{
		$this->reOccurrenceCount = $reOccurrenceCount;
	}
	
	/**
	 * @param ScheduleEventRecurrenceFrequency $frequency
	 */
	public function setFrequency($frequency)
	{
		$this->frequency = $frequency;
	}
}