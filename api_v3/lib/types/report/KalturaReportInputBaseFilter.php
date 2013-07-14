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
	 * Start day as string (YYYYMMDD)
	 *
	 * @var string
	 */
	public $fromDay;
	
	/**
	 * End date as string (YYYYMMDD)
	 *
	 * @var string
	 */
	public $toDay;
	
	
	
	/**
	 * @param reportsInputFilter $reportInputFilter
	 * @return reportsInputFilter
	 */
	public function toReportsInputFilter ($reportInputFilter = null)
	{
		if (is_null($reportInputFilter)) 
			$reportInputFilter = new reportsInputFilter();
		
		if ($this->fromDay && $this->toDay) {
			$reportInputFilter->from_date = strtotime(date('Y-m-d 00:00:00', strtotime($this->fromDay)));
			$reportInputFilter->to_date = strtotime(date('Y-m-d 23:59:59', strtotime($this->toDay)));
			$reportInputFilter->from_day = $this->fromDay;
			$reportInputFilter->to_day = $this->toDay;
		} else if ($this->fromDate && $this->toDate) {
			$reportInputFilter->from_date = $this->fromDate;
			$reportInputFilter->to_date = $this->toDate;
			$reportInputFilter->from_day = date ( "Ymd" , $this->fromDate );
			$reportInputFilter->to_day = date ( "Ymd" , $this->toDate );
		} else {
			$reportInputFilter->from_date = $this->fromDate;
			$reportInputFilter->to_date = $this->toDate;
			$reportInputFilter->from_day = $this->fromDay;
			$reportInputFilter->to_day = $this->toDay;
		}
		
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
		$this->fromDay = $reportInputFilter->from_day ;
		$this->toDay = $reportInputFilter->to_day ;
		
		return $this;
	}	
}
