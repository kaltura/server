<?php
/**
 * @package Admin
 * @subpackage Client
 */
class Kaltura_Client_SystemPartner_Type_SystemPartnerUsageFilter extends Kaltura_Client_Type_Filter
{
	public function getKalturaObjectType()
	{
		return 'KalturaSystemPartnerUsageFilter';
	}
	
	/**
	 * Date range from
	 * 
	 *
	 * @var int
	 */
	public $fromDate = null;

	/**
	 * Date range to
	 * 
	 *
	 * @var int
	 */
	public $toDate = null;


}

