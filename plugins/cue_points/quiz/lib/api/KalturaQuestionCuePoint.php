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


	/**
	 * @var KalturaQuestionType.
	 */
	public $questionType;


	public function __construct()
	{
		$this->cuePointType = QuizPlugin::getApiValue(QuizCuePointType::QUIZ_QUESTION);
	}

	private static $map_between_objects = array
	(
		"optionalAnswers",
		"hint",
		"question" => "name",
		"explanation",
		"questionType",
	);

	/* (non-PHPdoc)
	 * @see KalturaCuePoint::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

	/* (non-PHPdoc)
	* @see KalturaObject::toObject($object_to_fill, $props_to_skip)
	*/
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
		$dbEntry = entryPeer::retrieveByPK($dbObject->getEntryId());
		if ( !kEntitlementUtils::isEntitledForEditEntry($dbEntry) ) {
			foreach ( $this->optionalAnswers as $answer ) {
				$answer->isCorrect = KalturaNullableBoolean::NULL_VALUE;
			}
			$this->explanation = null;
		}
	}

	/* (non-PHPdoc)
	 * @see KalturaCuePoint::validateForInsert()
	 */
	public function validateForInsert($propertiesToSkip = array())
	{
		parent::validateForInsert($propertiesToSkip);
		$dbEntry = entryPeer::retrieveByPK($this->entryId);
		QuizPlugin::validateAndGetQuiz($dbEntry);
		if ( !kEntitlementUtils::isEntitledForEditEntry($dbEntry) ) {
			throw new KalturaAPIException(KalturaErrors::INVALID_USER_ID);
		}
	}

}
