<?php
/**
 * @package plugins.quiz
 * @subpackage api.objects
 */
class KalturaAnswerCuePoint extends KalturaCuePoint
{
	/**
	 * @var string
	 * @insertonly
	 */
	public $parentId;

	/**
	 * @var string
	 */
	public $quizUserEntryId;

	/**
	 * @var string
	 */
	public $answerKey;

	/**
	 * @var KalturaNullableBoolean
	 * @readonly
	 */
	public $isCorrect;

	/**
	 * Array of string
	 * @var KalturaTypedArray
	 * @readonly
	 */
	public $correctAnswerKeys;

	/**
	 * @var string
	 * @readonly
	 */
	public $explanation;


	public function __construct()
	{
		$this->cuePointType = QuizPlugin::getApiValue(QuizCuePointType::ANSWER);
	}

	private static $map_between_objects = array
	(
		"quizUserEntryId",
		"answerKey",
		"parentId",
		"correctAnswerKeys",
		"isCorrect",
		"explanation"
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
			$dbObject = new AnswerCuePoint();
		}

		return parent::toObject($dbObject, $propsToSkip);
	}

	/* (non-PHPdoc)
	 * @see KalturaObject::fromObject()
	 */
	public function doFromObject($dbObject, KalturaDetachedResponseProfile $responseProfile = null)
	{
		parent::doFromObject($dbObject, $responseProfile);

		$dbEntry = entryPeer::retrieveByPK($dbObject->getEntryId());
		if ( !QuizPlugin::validateUserEntitledForQuizEdit($dbEntry) ) {
			$kQuiz = QuizPlugin::validateAndGetQuiz( $dbEntry );
			//TODO if quiz status is submitted go according to showResultsOnSubmit flag

			if ( !$kQuiz->getShowResultOnAnswer() )
				$this->isCorrect = KalturaNullableBoolean::NULL_VALUE;

			if ( !$kQuiz->getShowCorrectKeyOnAnswer() ) {
				$this->correctAnswerKeys = null;
				$this->explanation = null;
			}
		}
	}

	/*
	 * @param string $cuePointId
	 * @throw KalturaAPIException - when parent cue points is missing or not a question cue point or doesn't belong to the same entry
	 */
	public function validateParentId($cuePointId = null)
	{
		if ($this->isNull('parentId'))
			throw new KalturaAPIException(KalturaQuizErrors::PARENT_ID_IS_MISSING);

		$dbParentCuePoint = CuePointPeer::retrieveByPK($this->parentId);
		if (!$dbParentCuePoint)
			throw new KalturaAPIException(KalturaCuePointErrors::PARENT_CUE_POINT_NOT_FOUND, $this->parentId);

		if (!($dbParentCuePoint instanceof QuestionCuePoint))
			throw new KalturaAPIException(KalturaQuizErrors::WRONG_PARENT_TYPE, $this->parentId);

		//not needed? insert only
//		if($cuePointId !== null){ // update
//			$dbCuePoint = CuePointPeer::retrieveByPK($cuePointId);
//			if(!$dbCuePoint)
//				throw new KalturaAPIException(KalturaCuePointErrors::INVALID_OBJECT_ID, $cuePointId);
//
//			if ($dbParentCuePoint->getEntryId() != $dbCuePoint->getEntryId())
//				throw new KalturaAPIException(KalturaCuePointErrors::PARENT_CUE_POINT_DO_NOT_BELONG_TO_THE_SAME_ENTRY);
//		}
//		else
//		{
			if ($dbParentCuePoint->getEntryId() != $this->entryId)
				throw new KalturaAPIException(KalturaCuePointErrors::PARENT_CUE_POINT_DO_NOT_BELONG_TO_THE_SAME_ENTRY);
//		}
	}

	/* (non-PHPdoc)
	 * @see KalturaCuePoint::validateForInsert()
	 */
	public function validateForInsert($propertiesToSkip = array())
	{
		parent::validateForInsert($propertiesToSkip);
		$dbEntry = entryPeer::retrieveByPK($this->entryId);
		QuizPlugin::validateAndGetQuiz($dbEntry);
		$this->validateParentId();

		//TODO do not allow answer with duplicate quizUserEntryId
	}

	/* (non-PHPdoc)
	 * @see KalturaCuePoint::validateForUpdate()
	 */
	public function validateForUpdate($sourceObject, $propertiesToSkip = array())
	{
		parent::validateForUpdate($sourceObject, $propertiesToSkip);
		$dbEntry = entryPeer::retrieveByPK($this->entryId);
		$kQuiz = QuizPlugin::validateAndGetQuiz($dbEntry);
		if ( !$kQuiz->getAllowAnswerUpdate() ) {
			throw new KalturaAPIException(KalturaQuizErrors::ANSWER_UPDATE_IS_NOT_ALLOWED, $sourceObject->getEntryId());
		}
	}
}
