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

	private static $map_between_objects = array
	(
		'application',
		'userIds',
		'playbackContext',
		'ancestorPlaybackContext'
	);

	protected function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

	public function toReportsInputFilter($reportsInputFilter = null)
	{
		if (!$reportsInputFilter)
			$reportsInputFilter = new endUserReportsInputFilter();

		return parent::toReportsInputFilter($reportsInputFilter);
	}
}