<?php

/**
 * @package plugins.game
 * @subpackage api.objects
 */
class KalturaUserScoreProperties extends KalturaObject
{
	/**
	 * @var int
	 */
	public $rank;
	
	/**
	 * @var string
	 */
	public $userId;
	
	/**
	 * @var int
	 */
	public $score;

	
	private static $map_between_objects = array
	(
		'rank',
		'userId',
		'score',
	);
	
	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
	
	public function toObject($object_to_fill = null, $props_to_skip = array())
	{
		if (!$object_to_fill)
			$object_to_fill = new UserScoreProperties();
		
		return parent::toObject($object_to_fill, $props_to_skip);
	}
}