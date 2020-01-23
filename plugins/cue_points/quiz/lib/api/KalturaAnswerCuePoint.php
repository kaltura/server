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
	* @var string
	* @maxLength 1024
	*/
	public $openAnswer;

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

	/**
	* @var string
	* @maxLength 1024
	*/
	public $feedback;


	public function __construct()
	{
		$this->cuePointType = QuizPlugin::getApiValue(QuizCuePointType::QUIZ_ANSWER);
	}

	private static $map_between_objects = array
	(
		"quizUserEntryId",
		"answerKey",
		"parentId",
		"correctAnswerKeys",
		"isCorrect",
		"explanation",
		"openAnswer",
		"feedback"
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
			/**
			 * @var kQuiz $kQuiz
			 */
			$kQuiz = QuizPlugin::validateAndGetQuiz( $dbEntry );

			$dbUserEntry = UserEntryPeer::retrieveByPK($this->quizUserEntryId);
			if ($dbUserEntry && $dbUserEntry->getStatus() == QuizPlugin::getCoreValue('UserEntryStatus', QuizUserEntryStatus::QUIZ_SUBMITTED))
			{
				if (!$kQuiz->getShowCorrectAfterSubmission())
				{
					$this->isCorrect = null;
					$this->correctAnswerKeys = null;
					$this->explanation = null;
				}
			}
			else
			{
				if (!$kQuiz->getShowCorrect()) {
					$this->isCorrect = null;
				}
				if (!$kQuiz->getShowCorrectKey())
				{
					$this->correctAnswerKeys = null;
					$this->explanation = null;
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

	protected function validateOnlyOneAnswer()
	{
		if(kCurrentContext::$ks_object && kCurrentContext::$ks_object->isAnonymousSession())
		{
			return;
		}

		if(kCurrentContext::$is_admin_session)
		{
			$dbUserEntry = UserEntryPeer::retrieveByPK($this->quizUserEntryId);
			$kuserId = $dbUserEntry->getKuserId();
		}
		else
		{
			$kuserId = kCurrentContext::getCurrentKsKuserId();
		}

		$cuePoints = CuePointPeer::retrieveCuePointAnswers($this->entryId, $this->parentId, $kuserId);
		foreach($cuePoints as $cuePoint)
		{
			/* @var $cuePoint AnswerCuePoint */
			if($cuePoint->getQuizUserEntryId() === $this->quizUserEntryId)
			{
				throw new KalturaAPIException(KalturaQuizErrors::ANSWER_ALREADY_EXISTS, $this->parentId, $this->quizUserEntryId);
			}
		}
	}


	protected function validateUserEntry()
	{
		$dbUserEntry = UserEntryPeer::retrieveByPK($this->quizUserEntryId);
		if (!$dbUserEntry)
			throw new KalturaAPIException(KalturaErrors::USER_ENTRY_NOT_FOUND, $this->quizUserEntryId);
		if ($dbUserEntry->getEntryId() !== $this->entryId)
		{
			throw new KalturaAPIException(KalturaCuePointErrors::USER_ENTRY_DOES_NOT_MATCH_ENTRY_ID, $this->quizUserEntryId);
		}
		if (!kCurrentContext::$is_admin_session)
		{
			if ($dbUserEntry->getStatus() === QuizPlugin::getCoreValue('UserEntryStatus', QuizUserEntryStatus::QUIZ_SUBMITTED))
			{
				throw new KalturaAPIException(KalturaQuizErrors::USER_ENTRY_QUIZ_ALREADY_SUBMITTED);
			}
			if ($dbUserEntry->getKuserId() != kCurrentContext::getCurrentKsKuserId()) 
			{
			    throw new KalturaAPIException(KalturaErrors::INVALID_USER_ID);
			}
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
		$this->validateParentId();
		$this->validateUserEntry();
		$this->validateOnlyOneAnswer();
		if ($this->feedback != null && !kEntitlementUtils::isEntitledForEditEntry($dbEntry) )
		{
			KalturaLog::debug('Insert feedback on answer cue point is allowed only with admin KS or entry owner or co-editor');
			throw new KalturaAPIException(KalturaErrors::INVALID_USER_ID);
		}
	}

	/* (non-PHPdoc)
	 * @see KalturaCuePoint::validateForUpdate()
	 */
	public function validateForUpdate($sourceObject, $propertiesToSkip = array())
	{
		parent::validateForUpdate($sourceObject, $propertiesToSkip);
		if(!$this->entryId)
		{
			throw new KalturaAPIException(KalturaErrors::PROPERTY_VALIDATION_CANNOT_BE_NULL, 'KalturaAnswerCuePoint:entryId');
		}
		$dbEntry = entryPeer::retrieveByPK($this->entryId);
		$kQuiz = QuizPlugin::validateAndGetQuiz($dbEntry);
		$this->validateUserEntry();
		if ( !$kQuiz->getAllowAnswerUpdate() && !kCurrentContext::$is_admin_session) 
		{
			throw new KalturaAPIException(KalturaQuizErrors::ANSWER_UPDATE_IS_NOT_ALLOWED, $sourceObject->getEntryId());
		}
		if ($this->feedback != null && !kEntitlementUtils::isEntitledForEditEntry($dbEntry) )
		{
			KalturaLog::debug('Update feedback on answer cue point is allowed only with admin KS or entry owner or co-editor');
			throw new KalturaAPIException(KalturaErrors::INVALID_USER_ID);
		}
	}
}
