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
	
	/**
	 * 
	 * @var string
	 */
	public $ancestorPlaybackContext;
	
	
	
	const APPLICATION_NAME_PLACE_HOLDER = "{APPLICATION_NAME}";
	const PUSERS_PLACE_HOLDER = "{PUSER_ID}";
	const UNKNOWN_PUSER_ID_CLAUSE = "'0'";
	const UNKNOWN_NAME_CLAUSE = "'Unknown'";
	
	public function toReportsInputFilter ($reportsInputFilter = null)
	{
		$endUserReportsInputFilter = new endUserReportsInputFilter();
		parent::toReportsInputFilter($endUserReportsInputFilter);
		$endUserReportsInputFilter->application = $this->application;
		$endUserReportsInputFilter->userIds = $this->userIds;
		$endUserReportsInputFilter->playbackContext = $this->playbackContext;
		$endUserReportsInputFilter->ancestorPlaybackContext = $this->ancestorPlaybackContext;
		
		
		if ($this->application != null) {
			$endUserReportsInputFilter->extra_map[self::APPLICATION_NAME_PLACE_HOLDER] = "'" . $this->application . "'";
		} 
		if ($this->userIds != null) {
			$objectIds = explode(',', $this->userIds);
			$puserIds = "('" . implode("','", $objectIds) . "')";
			// replace puser_id '0' with 'Unknown' as it saved on dwh pusers table
			$puserIds = str_replace(self::UNKNOWN_PUSER_ID_CLAUSE, self::UNKNOWN_NAME_CLAUSE, $puserIds);
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
		$this->ancestorPlaybackContext = $endUserReportsInputFilter->ancestorPlaybackContext;	
		return $this;
	}	
}