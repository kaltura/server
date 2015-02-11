<?php
/**
 * @package plugins.systemPartner
 * @subpackage api.objects
 */
class KalturaSystemPartnerUsageFilter extends KalturaFilter
{
	/**
	 * Date range from
	 * 
	 * @var int
	 */
	public $fromDate;
	
	/**
	 * Date range to
	 * 
	 * @var int
	 */
	public $toDate;
	
	/**
	 * Time zone offset
	 * @var int
	 */
	public $timezoneOffset;

	/* (non-PHPdoc)
	 * @see KalturaFilter::getCoreFilter()
	 */
	protected function getCoreFilter()
	{
		return new partnerFilter();
	}
}