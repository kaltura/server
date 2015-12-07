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
	
	
	public function toReportsInputFilter ($reportsInputFilter = null)
	{
		$endUserReportsInputFilter = new endUserReportsInputFilter();
		parent::toReportsInputFilter($endUserReportsInputFilter);
		$endUserReportsInputFilter->application = $this->application;
		$endUserReportsInputFilter->userIds = $this->userIds;
		$endUserReportsInputFilter->playbackContext = $this->playbackContext;
		$endUserReportsInputFilter->ancestorPlaybackContext = $this->ancestorPlaybackContext;
			
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