<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaLiveReportExportJobData extends KalturaJobData
{
	/**
	 * @var time
	 */
	public $timeReference; 
	
	/**
	 * @var string
	 */
	public $entryIds;
	
	/**
	 * @var string
	 */
	public $outputPath;
	
	/**
	 * @var string
	 */
	public $recipientEmail;
	
	private static $map_between_objects = array
	(
			"timeReference" ,
			"entryIds" ,
			'outputPath',
			"recipientEmail",
	);
	
	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
	
	public function toObject($dbData = null, $props_to_skip = array()) 
	{
		if(is_null($dbData))
			$dbData = new kLiveReportExportJobData();
			
		return parent::toObject($dbData, $props_to_skip);
	}
}
