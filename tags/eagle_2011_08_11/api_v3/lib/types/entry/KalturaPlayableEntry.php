<?php 
/**
 * @package api
 * @subpackage objects
 */
class KalturaPlayableEntry extends KalturaBaseEntry
{
	/**
	 * Number of plays
	 * 
	 * @var int
	 * @readonly
	 * @filter order
	 */
	public $plays;
	
	/**
	 * Number of views
	 * 
	 * @var int
	 * @readonly
	 * @filter order
	 */
	public $views;
	
	/**
	 * The width in pixels
	 * 
	 * @var int
	 * @readonly
	 */
	public $width;
	
	/**
	 * The height in pixels
	 * 
	 * @var int
	 * @readonly
	 */
	public $height;
	
	/**
	 * The duration in seconds
	 * 
	 * @var int
	 * @readonly
	 * @filter lt,gt,lte,gte,order
	 */
	public $duration;
	
	/**
	 * The duration in miliseconds
	 * 
	 * @var int
	 * @readonly
	 * @filter lt,gt,lte,gte,order
	 */
	public $msDuration;
	
	/**
	 * The duration type (short for 0-4 mins, medium for 4-20 mins, long for 20+ mins)
	 * 
	 * @var KalturaDurationType
	 * @readonly
	 * @filter matchor
	 */
	public $durationType;
	
	private static $map_between_objects = array
	(
		"plays",
		"views",
		"width",
		"height",
		"msDuration" => "lengthInMsecs",
		"duration" => "durationInt"
	);
	
	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
}