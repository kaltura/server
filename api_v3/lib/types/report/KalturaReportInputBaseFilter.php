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

	private static $map_between_objects = array
	(
		'fromDate' => 'from_date',
		'toDate' => 'to_date',
		'fromDay' => 'from_day',
		'toDay' => 'to_day',
	);

	protected function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

	/**
	 * @param reportsInputFilter $reportInputFilter
	 * @return reportsInputFilter
	 */
	public function toReportsInputFilter($reportInputFilter = null)
	{
		if (!$reportInputFilter)
			$reportInputFilter = new reportsInputFilter();

		if ($this->fromDay && $this->toDay) {
			$this->fromDate = strtotime(date('Y-m-d 00:00:00', strtotime($this->fromDay)));
			$this->toDate = strtotime(date('Y-m-d 23:59:59', strtotime($this->toDay)));
		}

		foreach ($this->getMapBetweenObjects() as $apiName => $memberName)
		{
			if (is_numeric($apiName)) {
				$apiName = $memberName;
			}
			$reportInputFilter->$memberName = $this->$apiName;
		}
		return $reportInputFilter;
	}
}
