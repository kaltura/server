<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaReportInputFilter extends KalturaObject 
{
	/**
	 * Start date as Unix timestamp (In seconds)
	 * 
	 * @var int
	 */
	public $fromDate;
	
	/**
	 * End date as Unix timestamp (In seconds)
	 * 
	 * @var int
	 */
	public $toDate;
	
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
	
	public function toReportsInputFilter ($reportInputFilter = null)
	{
		if (is_null($reportInputFilter)) 
			$reportInputFilter = new reportsInputFilter();
		
		$reportInputFilter->from_date = $this->fromDate;
		$reportInputFilter->to_date = $this->toDate;
		$reportInputFilter->keywords = $this->keywords;
		$reportInputFilter->search_in_tags= $this->searchInTags;
		$reportInputFilter->search_in_admin_tags = $this->searchInAdminTags;
		$reportInputFilter->categories = $this->categories;
		$reportInputFilter->timeZoneOffset = $this->timeZoneOffset;
		$reportInputFilter->interval = $this->interval;
		
		return $reportInputFilter;
	}
	
	public function fromReportsInputFilter (  $reportInputFilter )
	{
		$this->fromDate = $reportInputFilter->from_date ;
		$this->toDate = $reportInputFilter->to_date ;
		$this->keywords = $reportInputFilter->keywords ;
		$this->searchInTags = $reportInputFilter->search_in_tags ;
		$this->searchInAdminTags = $reportInputFilter->search_in_admin_tags ;
		$this->categories=$reportInputFilter->categories;
		$this->timeZoneOffset=$reportInputFilter->timeZoneOffset;
		
		return $this;
	}	
}