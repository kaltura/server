<?php
/**
 * @package api
 * @subpackage filters
 */
class KalturaReportInputBaseFilter extends KalturaObject 
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
	 * @param reportsInputFilter $reportInputFilter
	 * @return reportsInputFilter
	 */
	public function toReportsInputFilter ($reportInputFilter = null)
	{
		if (is_null($reportInputFilter)) 
			$reportInputFilter = new reportsInputFilter();
		
		$reportInputFilter->from_date = $this->fromDate;
		$reportInputFilter->to_date = $this->toDate;
		
		return $reportInputFilter;
	}
	
	/**
	 * @param reportsInputFilter $reportInputFilter
	 * @return KalturaReportInputBaseFilter
	 */
	public function fromReportsInputFilter (  $reportInputFilter )
	{
		$this->fromDate = $reportInputFilter->from_date ;
		$this->toDate = $reportInputFilter->to_date ;
		
		return $this;
	}	
}
