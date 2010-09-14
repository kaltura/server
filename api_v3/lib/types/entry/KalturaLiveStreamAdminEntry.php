<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaLiveStreamAdminEntry extends KalturaLiveStreamEntry
{
	/**
	 * The broadcast primary ip
	 * 
	 * @var string
	 */
	public $encodingIP1;
	
	/**
	 * The broadcast secondary ip
	 * 
	 * @var string
	 */
	public $encodingIP2;
	
	/**
	 * The broadcast password
	 * 
	 * @var string
	 */
	public $streamPassword;
	
	/**
	 * The broadcast username
	 * 
	 * @var string
	 * @readonly
	 */
	public $streamUsername;
	
	
	private static $map_between_objects = array
	(
		"encodingIP1",
		"encodingIP2",
		"streamPassword",
		"streamUsername",
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
}
?>