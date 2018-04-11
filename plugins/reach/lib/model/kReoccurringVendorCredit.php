
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
	 *  @var VendorCreditRecurrenceFrequency
	 */
	protected $frequency;

	/**
	 *  @var string
	 */
	protected $finalEndDate;


	/**
	 * @param string $toDate
	 */
	public function setToDate($toDate)
	{
		$this->finalEndDate = $toDate;
		parent::setToDate($this->calculateNextPeriodEndDate());
	}

	/**
	 * @param string $toDate
	 */
	public function getToDate()
	{
		return $this->finalEndDate;
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

	public function syncCredit($reachProfileId)
	{
		$syncedCredit = parent::syncCredit($reachProfileId);
		if ( $this->getLastSyncTime() > $this->getToDate() )
		{
			$this->calculateNextPeriodEndDate();
			$this->setSyncedCredit(0);
		}
		return $syncedCredit;
	}

	private function calculateNextPeriodEndDate()
	{
		$newTime = strtotime('+'.$this->getFrequency(), strtotime($this->getToDate()));
		parent::setToDate(min ($newTime , $this->finalEndDate));
	}

}
