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


	public function __construct()
	{
		$this->cuePointType = QuizPlugin::getApiValue(QuizCuePointType::ANSWER);
	}

	private static $map_between_objects = array
	(
		"quizUserEntryId",
		"answerKey",
		"parentId",
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

		$questionCp = CuePointPeer::retrieveByPK($dbObject->getParentId());
		if ( !$questionCp->isEntitledForCompleteInfo( $this->entryId ) ) {
			$dbEntry = entryPeer::retrieveByPK($this->entryId);
			if ( !$dbEntry )
				throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $this->entryId);

			//TODO TBD also check quiz status?? TBD explanation
			$kQuiz = $this->getQuiz($dbEntry);

			if ( !$kQuiz->getShowResultOnAnswer() )
				$this->isCorrect = KalturaNullableBoolean::NULL_VALUE;

			if ( !$kQuiz->getShowCorrectKeyOnAnswer() )
				$this->correctAnswerKeys = null;

		}
	}

	/**
	 * @param $entry
	 * @return mixed|string
	 * @throws KalturaAPIException
	 */
	private function getQuiz( $entry ) {
		$kQuiz = QuizPlugin::getQuizData($entry);
		if ( !$kQuiz )
			throw new KalturaAPIException(KalturaQuizErrors::PROVIDED_ENTRY_IS_NOT_A_QUIZ, $entry->getEntryId());

		return $kQuiz;
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

		if($cuePointId !== null){ // update
			$dbCuePoint = CuePointPeer::retrieveByPK($cuePointId);
			if(!$dbCuePoint)
				throw new KalturaAPIException(KalturaCuePointErrors::INVALID_OBJECT_ID, $cuePointId);

			if ($dbParentCuePoint->getEntryId() != $dbCuePoint->getEntryId())
				throw new KalturaAPIException(KalturaCuePointErrors::PARENT_CUE_POINT_DO_NOT_BELONG_TO_THE_SAME_ENTRY);
		}
		else
		{
			if ($dbParentCuePoint->getEntryId() != $this->entryId)
				throw new KalturaAPIException(KalturaCuePointErrors::PARENT_CUE_POINT_DO_NOT_BELONG_TO_THE_SAME_ENTRY);
		}
	}

//	/* (non-PHPdoc)
//	 * @see KalturaObject::toInsertableObject()
//	 */
//	public function toInsertableObject($object_to_fill = null, $props_to_skip = array())
//	{
//		if(is_null($object_to_fill))
//			$object_to_fill = new AnswerCuePoint();
//
//		return parent::toInsertableObject($object_to_fill, $props_to_skip);
//	}

	/* (non-PHPdoc)
	 * @see KalturaCuePoint::validateForInsert()
	 */
	public function validateForInsert($propertiesToSkip = array())
	{
		parent::validateForInsert($propertiesToSkip);

		$this->validateParentId();

		//TODO do not allow answer with duplicate answersUserEntryId
	}

	/* (non-PHPdoc)
	 * @see KalturaCuePoint::validateForUpdate()
	 */
	public function validateForUpdate($sourceObject, $propertiesToSkip = array())
	{
		parent::validateForUpdate($sourceObject, $propertiesToSkip);

		$dbEntry = entryPeer::retrieveByPK($sourceObject->getEntryId());
		$kQuiz = $this->getQuiz($dbEntry);
		if ( !$kQuiz->getAllowAnswerUpdate() ) {
			throw new KalturaAPIException(KalturaQuizErrors::ANSWER_UPDATE_IS_NOT_ALLOWED, $sourceObject->getEntryId());
		}

		$this->validateParentId($sourceObject->getId());
	}
}
