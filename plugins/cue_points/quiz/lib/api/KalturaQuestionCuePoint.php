<?php
/**
 * @package plugins.quiz
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
	 * Array of string
	 * @var KalturaStringArray
	 */
	public $correctAnswerKeys;


	/**
	 * @var string
	 */
	public $hint;


	/**
	 * @var string
	 * @filter like,mlikeor,mlikeand
	 */
	public $question;

	/**
	 * @var string
	 */
	public $explanation;


	public function __construct()
	{
		$this->cuePointType = QuizPlugin::getApiValue(QuizCuePointType::QUESTION);
	}

	private static $map_between_objects = array
	(
		"optionalAnswers",
		"correctAnswerKeys",
		"hint",
		"question" => "name",
		"explanation",
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
//	public function toInsertableObject($object_to_fill = null, $props_to_skip = array())
//	{
//		if(is_null($object_to_fill))
//			$object_to_fill = new QuestionCuePoint();
//
//		return parent::toInsertableObject($object_to_fill, $props_to_skip);
//	}

	/* (non-PHPdoc)
 * 	* @see KalturaObject::toObject($object_to_fill, $props_to_skip)
 *	*/
	public function toObject($dbObject = null, $propsToSkip = array())
	{
		if (!$dbObject)
		{
			$dbObject = new QuestionCuePoint();
		}

		return parent::toObject($dbObject, $propsToSkip);
	}

	/* (non-PHPdoc)
	 * @see KalturaObject::fromObject()
	 */
	public function doFromObject($dbObject, KalturaDetachedResponseProfile $responseProfile = null)
	{
		parent::doFromObject($dbObject, $responseProfile);
		$this->optionalAnswers = KalturaOptionalAnswersArray::fromDbArray($dbObject->getOptionalAnswers(), $responseProfile);

		if ( !$dbObject->isEntitledForCompleteInfo( $this->entryId ) ) {
			$this->correctAnswerKeys = null;
			$this->explanation = null; //?
			//TODO hint?
		}
	}

	/* (non-PHPdoc)
	 * @see KalturaCuePoint::validateForInsert()
	 */
//	public function validateForInsert($propertiesToSkip = array())
//	{
//		parent::validateForInsert($propertiesToSkip);
//
//		$this->validateEndTime();
//	}
//
//	/* (non-PHPdoc)
//	 * @see KalturaCuePoint::validateForUpdate()
//	 */
//	public function validateForUpdate($sourceObject, $propertiesToSkip = array())
//	{
//		return parent::validateForUpdate($sourceObject, $propertiesToSkip);
//	}

}
