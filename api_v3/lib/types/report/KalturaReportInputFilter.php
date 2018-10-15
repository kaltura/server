<?php
/**
 * @package api
 * @subpackage filters
 */
class KalturaReportInputFilter extends KalturaReportInputBaseFilter 
{
	/**
	 * Search keywords to filter objects
	 * 
	 * @var string
	 */
	public $keywords;
	
	/**
	 * Search keywords in onjects tags
	 * 
	 * @var bool
	 */
	public $searchInTags;	

	/**
	 * Search keywords in onjects admin tags
	 * 
	 * @var bool
	 * @deprecated
	 */
	public $searchInAdminTags;	
	
	/**
	 * Search onjects in specified categories
	 * 
	 * @var string
	 */
	public $categories;
	
	/**
	 * Filter by customVar1
	 *
	 * @var string
	 */
	public $customVar1In;

	/**
	 * Filter by customVar2
	 *
	 * @var string
	 */
	public $customVar2In;

	/**
	 * Filter by customVar3
	 *
	 * @var string
	 */
	public $customVar3In;

	/**
	 * Time zone offset in minutes
	 * 
	 * @var int
	 */
	public $timeZoneOffset = 0;
	
	/**
	 * Aggregated results according to interval
	 * 
	 * @var KalturaReportInterval
	 */
	public $interval;
	
	/* (non-PHPdoc)
	 * @see KalturaReportInputBaseFilter::toReportsInputFilter()
	 */
	public function toReportsInputFilter($reportInputFilter = null)
	{
		$reportInputFilter = parent::toReportsInputFilter($reportInputFilter);
		
		$reportInputFilter->keywords = $this->keywords;
		$reportInputFilter->search_in_tags= $this->searchInTags;
		$reportInputFilter->search_in_admin_tags = $this->searchInAdminTags;
		$reportInputFilter->categories = $this->categories;
		$reportInputFilter->custom_var1 = $this->customVar1In;
		$reportInputFilter->custom_var2 = $this->customVar2In;
		$reportInputFilter->custom_var3 = $this->customVar3In;
		$reportInputFilter->timeZoneOffset = $this->timeZoneOffset;
		$reportInputFilter->interval = $this->interval;
		
		return $reportInputFilter;
	}
	
	/* (non-PHPdoc)
	 * @see KalturaReportInputBaseFilter::fromReportsInputFilter()
	 */
	public function fromReportsInputFilter($reportInputFilter )
	{
		parent::fromReportsInputFilter($reportInputFilter);
		
		$this->keywords = $reportInputFilter->keywords ;
		$this->searchInTags = $reportInputFilter->search_in_tags ;
		$this->searchInAdminTags = $reportInputFilter->search_in_admin_tags ;
		$this->categories = $reportInputFilter->categories;
		$this->customVar1In = $reportInputFilter->custom_var1;
		$this->customVar2In = $reportInputFilter->custom_var2;
		$this->customVar3In = $reportInputFilter->custom_var3;
		$this->timeZoneOffset = $reportInputFilter->timeZoneOffset;
		
		return $this;
	}	
}
