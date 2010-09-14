<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaReportInputFilter extends KalturaObject 
{
	/**
	 * @var int
	 */
	public $fromDate;
	
	/**
	 * @var int
	 */
	public $toDate;
	
	/**
	 * 
	 * @var string
	 */
	public $keywords;
	
	/**
	 * @var bool
	 */
	public $searchInTags;	

	/**
	 * @var bool
	 */
	public $searchInAdminTags;	
	
	/**
	 * 
	 * @var string
	 */
	public $categories;
	
	public function toReportsInputFilter ()
	{
		$reportInputFilter = new reportsInputFilter();
		$reportInputFilter->from_date = $this->fromDate;
		$reportInputFilter->to_date = $this->toDate;
		$reportInputFilter->keywords = $this->keywords;
		$reportInputFilter->search_in_tags= $this->searchInTags;
		$reportInputFilter->search_in_admin_tags= $this->searchInAdminTags;
		$reportInputFilter->categories=$this->categories;
		
		return $reportInputFilter;
	}
	
	public function fromReportsInputFilter ( reportsInputFilter $reportInputFilter )
	{
		$this->fromDate = $reportInputFilter->from_date ;
		$this->toDate = $reportInputFilter->to_date ;
		$this->keywords = $reportInputFilter->keywords ;
		$this->searchInTags = $reportInputFilter->search_in_tags ;
		$this->searchInAdminTags = $reportInputFilter->search_in_admin_tags ;
		$this->categories=$reportInputFilter->categories;
		
		return $this;
	}	
}