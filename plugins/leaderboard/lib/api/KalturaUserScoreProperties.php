<?php

/**
 * @package plugins.leaderboard
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
	
	/**
	 * @var string
	 */
	public $scoreTags;
	
	/**
	 * @var int
	 */
	public $oldRank;
	
	private static $map_between_objects = array
	(
		'rank',
		'userId',
		'score',
		'scoreTags',
		'oldRank',
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