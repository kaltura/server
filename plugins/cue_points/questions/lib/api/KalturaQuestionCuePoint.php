<?php
/**
 * @package plugins.questions
 * @subpackage api.objects
 */
class KalturaQuestionCuePoint extends KalturaCuePoint
{

	/**
	 * Array of key value answerKey->optionAnswer objects
	 * @var KalturaOptionalAnswersArray
	 */
	public $optionalAnswers;

	/**
	 * Array of int
	 * @var KalturaTypedArray
	 */
	public $correctAnswerKeys;


	/**
	 * @var string
	 * @requiresPermission insert,update
	 */
	public $hint;


	/**
	 * @var string
	 * @filter like,mlikeor,mlikeand
	 * @requiresPermission insert,update
	 */
	public $question;

	/**
	 * @var string
	 * @requiresPermission insert,update
	 */
	public $correctAnswerExplanation;


	public function __construct()
	{
		$this->cuePointType = questionsPlugin::getApiValue(QuestionsCuePointType::QUESTION);
	}

	private static $map_between_objects = array
	(
		"optionalAnswers",
		"correctAnswerKeys",
		"hint",
		"question" => "name",
		"correctAnswerExplanation",
	);

	/* (non-PHPdoc)
	 * @see KalturaCuePoint::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

	/* (non-PHPdoc)
	 * @see KalturaObject::toInsertableObject()
	 */
	public function toInsertableObject($object_to_fill = null, $props_to_skip = array())
	{
		if(is_null($object_to_fill))
			$object_to_fill = new AdCuePoint();

		return parent::toInsertableObject($object_to_fill, $props_to_skip);
	}

	/* (non-PHPdoc)
	 * @see KalturaCuePoint::validateForInsert()
	 */
	public function validateForInsert($propertiesToSkip = array())
	{
		parent::validateForInsert($propertiesToSkip);

		$this->validateEndTime();
	}

	/* (non-PHPdoc)
	 * @see KalturaCuePoint::validateForUpdate()
	 */
	public function validateForUpdate($sourceObject, $propertiesToSkip = array())
	{
		$this->validateEndTime($sourceObject);

		return parent::validateForUpdate($sourceObject, $propertiesToSkip);
	}
}
