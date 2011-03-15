<?php


/**
 * Skeleton subclass for representing a row from the 'dwh_hourly_partner' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package plugins.partnerAggregation
 * @subpackage model
 */
class DwhHourlyPartner extends BaseDwhHourlyPartner {

	/**
	 * Initializes internal state of DwhHourlyPartner object.
	 * @see        parent::__construct()
	 */
	public function __construct()
	{
		// Make sure that parent constructor is always invoked, since that
		// is where any default values for this object are set.
		parent::__construct();
	}

	public function getAggregatedTime()
	{
		$dateId = $this->getDateId();
		return mktime(
			$this->getHourId(), 0, 0, 
			substr($dateId, 4, 2), 
			substr($dateId, 6, 2), 
			substr($dateId, 0, 4));
	}
	
} // DwhHourlyPartner
