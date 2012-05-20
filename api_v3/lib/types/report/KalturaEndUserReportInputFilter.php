<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaEndUserReportInputFilter extends KalturaReportInputFilter 
{
	
	/**
	 * 
	 * @var string
	 */
	public $application;
	
	/**
	 * 
	 * @var string
	 */
	public $userIds;	
	
	/**
	 * 
	 * @var string
	 */
	public $playbackContext;
	
	
	
	const APPLICATION_NAME_PLACE_HOLDER = "{APPLICATION_NAME}";
	const PUSERS_PLACE_HOLDER = "{PUSER_ID}";
	
	public function toReportsInputFilter ($reportsInputFilter = null)
	{
		$endUserReportsInputFilter = new endUserReportsInputFilter();
		parent::toReportsInputFilter($endUserReportsInputFilter);
		$endUserReportsInputFilter->application = $this->application;
		$endUserReportsInputFilter->userIds = $this->userIds;
		$endUserReportsInputFilter->playbackContext = $this->playbackContext;
		
		
		if ($this->application) {
			$endUserReportsInputFilter->extra_map[self::APPLICATION_NAME_PLACE_HOLDER] = "'" . $this->application . "'";
		} 
		if ($this->userIds) {
			$objectIds = explode(',', $this->userIds);
			$puserIds = "('" . implode("','", $objectIds) . "')";
			$endUserReportsInputFilter->extra_map[self::PUSERS_PLACE_HOLDER] = $puserIds;
		}
		return $endUserReportsInputFilter;
	}
	
	public function fromReportsInputFilter (  $endUserReportsInputFilter )
	{
		parent::fromReportsInputFilter($endUserReportsInputFilter);
		$this->application = $endUserReportsInputFilter->application ;
		$this->userIds = $endUserReportsInputFilter->userIds ;
		$this->playbackContext = $endUserReportsInputFilter->playbackContext;	
		return $this;
	}	
}