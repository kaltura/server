<?php
/**
 * @package plugins.quiz
 * @subpackage api.objects
 */
class KalturaAnswerCuePoint extends KalturaCuePoint
{
	/**
	 * @var string
	 * @filter eq,in
	 * @insertonly
	 */
	public $parentId;

	/**
	 * @var string
	 * @filter eq,in
	 * @insertonly
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
	 * @var KalturaStringArray
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
		$this->cuePointType = QuizPlugin::getApiValue(QuizCuePointType::QUIZ_ANSWER);
	}

	private static $map_between_objects = array
	(
		"quizUserEntryId",
		"answerKey",
		"parentId",
		"isCorrect",
		"correctAnswerKeys",
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
		if ( !kEntitlementUtils::isEntitledForEditEntry($dbEntry))
		{
			$this->validateParentId();
			$dbQuestionCuePoint = CuePointPeer::retrieveByPK($this->parentId);
			$questCp = new KalturaQuestionCuePoint();
			$questCp->fromObject($dbQuestionCuePoint);

			$this->explanation = $questCp->explanation;
			$this->setIsCorrectAnswer($dbObject);
			$this->setCorrectAnswerKeys($dbObject, $responseProfile);
		}
	}

	private function setCorrectAnswerKeys($dbObject, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$dbEntry = entryPeer::retrieveByPK($dbObject->getEntryId());
		/**
		 * @var kQuiz $kQuiz
		 */
		$kQuiz = QuizPlugin::validateAndGetQuiz( $dbEntry );
		$dbQuestionCuePoint = CuePointPeer::retrieveByPK($this->parentId);

		if ($kQuiz->getShowCorrectKey())
		{
			$this->correctAnswerKeys = null;
			foreach ($dbQuestionCuePoint->getOptionalAnswers() as $obj)
			{
				$optAnswer = new KalturaOptionalAnswer();
				$optAnswer->fromObject($obj, $responseProfile);
				if ($optAnswer->isCorrect)
				{
					if (is_null($this->correctAnswerKeys))
					{
						$this->correctAnswerKeys = new KalturaOptionalAnswersArray();
					}
					$this->correctAnswerKeys[] = $optAnswer;
				}
			}
		}
	}

	private function setIsCorrectAnswer($dbObject)
	{
		$dbEntry = entryPeer::retrieveByPK($dbObject->getEntryId());
		/**
		 * @var kQuiz $kQuiz
		 */
		$kQuiz = QuizPlugin::validateAndGetQuiz( $dbEntry );
		$dbQuestionCuePoint = CuePointPeer::retrieveByPK($this->parentId);

		if ($kQuiz->getShowCorrect())
		{

			$optionalAnswers = KalturaOptionalAnswersArray::fromDbArray($dbQuestionCuePoint->getOptionalAnswers());
			$this->isCorrect = false;
			/**
			 * @var kOptionalAnswer $optAns
			 */
			foreach ($optionalAnswers as $optAnswer)
			{
				if ($optAnswer->isCorrect && $this->answerKey == $optAnswer->key)
				{
					$this->isCorrect = true;
					return;
				}
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

		if ($dbParentCuePoint->getEntryId() != $this->entryId)
			throw new KalturaAPIException(KalturaCuePointErrors::PARENT_CUE_POINT_DO_NOT_BELONG_TO_THE_SAME_ENTRY);

	}

	protected function validateUserEntry()
	{
		$dbUserEntry = UserEntryPeer::retrieveByPK($this->quizUserEntryId);
		if (!$dbUserEntry)
			throw new KalturaAPIException(KalturaErrors::USER_ENTRY_NOT_FOUND, $this->quizUserEntryId);

		if ($dbUserEntry->getEntryId() !== $this->entryId)
			throw new KalturaAPIException(KalturaCuePointErrors::USER_ENTRY_DOES_NOT_MATCH_ENTRY_ID, $this->quizUserEntryId);

		if ($dbUserEntry->getStatus() === QuizPlugin::getCoreValue('UserEntryStatus', QuizUserEntryStatus::QUIZ_SUBMITTED))
			throw new KalturaAPIException(KalturaQuizErrors::USER_ENTRY_QUIZ_ALREADY_SUBMITTED);

		if (!kCurrentContext::$is_admin_session && ($dbUserEntry->getKuserId() != kCurrentContext::getCurrentKsKuserId()) )
		    throw new KalturaAPIException(KalturaErrors::INVALID_USER_ID);
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
		$this->validateUserEntry();
	}

	/* (non-PHPdoc)
	 * @see KalturaCuePoint::validateForUpdate()
	 */
	public function validateForUpdate($sourceObject, $propertiesToSkip = array())
	{
		parent::validateForUpdate($sourceObject, $propertiesToSkip);
		$dbEntry = entryPeer::retrieveByPK($this->entryId);
		$kQuiz = QuizPlugin::validateAndGetQuiz($dbEntry);
		$this->validateUserEntry();
		if ( !$kQuiz->getAllowAnswerUpdate() ) {
			throw new KalturaAPIException(KalturaQuizErrors::ANSWER_UPDATE_IS_NOT_ALLOWED, $sourceObject->getEntryId());
		}
	}
}
