<?php

/**
 * @package api
 * @subpackage objects
  */
class KalturaQuizUserEntry extends KalturaUserEntry{

	/**
	 * @var int
	 * @readonly
	 */
	public $score;
	/**
	 * Array of string
	 * @var KalturaStringArray
	 * @readonly
	 */
	public $correctAnswerKeys;


	private static $map_between_objects = array
	(
		"score",
		"correctAnswerKeys"
	);

	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}

	/**
	 * KalturaQuizUserEntry constructor.
	 */
	public function __construct()
	{
		$this->type = QuizPlugin::getCoreValue('UserEntryType', QuizUserEntryType::QUIZ);
	}

	public function toObject($object_to_fill = null, $props_to_skip = array())
	{
		if (is_null($object_to_fill))
		{
			$object_to_fill = new QuizUserEntry();
		}
		return parent::toObject($object_to_fill, $props_to_skip);
	}

}