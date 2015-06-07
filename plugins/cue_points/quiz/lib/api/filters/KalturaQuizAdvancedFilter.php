<?php
/**
 * @package plugins.quiz
 * @subpackage api.filters
 */
class KalturaQuizAdvancedFilter extends KalturaSearchItem
{
	/**
	 * @var KalturaNullableBoolean
	 */
	public $isQuiz;

	private static $map_between_objects = array
	(
		"isQuiz",
	);

	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}

	public function toObject ( $object_to_fill = null , $props_to_skip = array() )
	{
		if(!$object_to_fill)
			$object_to_fill = new kQuizAdvancedFilter();

		return parent::toObject($object_to_fill, $props_to_skip);
	}

}
