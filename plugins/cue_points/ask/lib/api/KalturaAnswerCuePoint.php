<?php
/**
 * @package plugins.ask
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
	public $askUserEntryId;

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
		$this->cuePointType = AskPlugin::getApiValue(AskCuePointType::ASK_ANSWER);
	}

	private static $map_between_objects = array
	(
		"askUserEntryId",
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
		if ( !kEntitlementUtils::isEntitledForEditEntry($dbEntry))
		{
			/**
			 * @var kAsk $kAsk
			 */
			$kAsk = AskPlugin::validateAndGetAsk( $dbEntry );

			$dbUserEntry = UserEntryPeer::retrieveByPK($this->askUserEntryId);
			if ($dbUserEntry && $dbUserEntry->getStatus() == AskPlugin::getCoreValue('UserEntryStatus', AskUserEntryStatus::ASK_SUBMITTED))
			{
				if (!$kAsk->getShowCorrectAfterSubmission())
				{
					$this->isCorrect = null;
					$this->correctAnswerKeys = null;
					$this->explanation = null;
				}
			}
			else
			{
				if (!$kAsk->getShowCorrect()) {
					$this->isCorrect = null;
				}
				if (!$kAsk->getShowCorrectKey())
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
			throw new KalturaAPIException(KalturaAskErrors::PARENT_ID_IS_MISSING);

		$dbParentCuePoint = CuePointPeer::retrieveByPK($this->parentId);
		if (!$dbParentCuePoint)
			throw new KalturaAPIException(KalturaCuePointErrors::PARENT_CUE_POINT_NOT_FOUND, $this->parentId);

		if (!($dbParentCuePoint instanceof QuestionCuePoint))
			throw new KalturaAPIException(KalturaAskErrors::WRONG_PARENT_TYPE, $this->parentId);

		if ($dbParentCuePoint->getEntryId() != $this->entryId)
			throw new KalturaAPIException(KalturaCuePointErrors::PARENT_CUE_POINT_DO_NOT_BELONG_TO_THE_SAME_ENTRY);

	}

	protected function validateUserEntry()
	{
		$dbUserEntry = UserEntryPeer::retrieveByPK($this->askUserEntryId);
		if (!$dbUserEntry)
			throw new KalturaAPIException(KalturaErrors::USER_ENTRY_NOT_FOUND, $this->askUserEntryId);
		if ($dbUserEntry->getEntryId() !== $this->entryId)
		{
			throw new KalturaAPIException(KalturaCuePointErrors::USER_ENTRY_DOES_NOT_MATCH_ENTRY_ID, $this->askUserEntryId);
		}
		if ($dbUserEntry->getStatus() === AskPlugin::getCoreValue('UserEntryStatus', AskUserEntryStatus::ASK_SUBMITTED))
		{
			throw new KalturaAPIException(KalturaAskErrors::USER_ENTRY_ASK_ALREADY_SUBMITTED);
		}
		if (!kCurrentContext::$is_admin_session && ($dbUserEntry->getKuserId() != kCurrentContext::getCurrentKsKuserId()) ) {
		    throw new KalturaAPIException(KalturaErrors::INVALID_USER_ID);
		}
	}

	/* (non-PHPdoc)
	 * @see KalturaCuePoint::validateForInsert()
	 */
	public function validateForInsert($propertiesToSkip = array())
	{
		parent::validateForInsert($propertiesToSkip);
		$dbEntry = entryPeer::retrieveByPK($this->entryId);
		AskPlugin::validateAndGetAsk($dbEntry);
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
		$kAsk = AskPlugin::validateAndGetAsk($dbEntry);
		$this->validateUserEntry();
		if ( !$kAsk->getAllowAnswerUpdate() ) {
			throw new KalturaAPIException(KalturaAskErrors::ANSWER_UPDATE_IS_NOT_ALLOWED, $sourceObject->getEntryId());
		}
	}
}
