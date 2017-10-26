<?php

/**
 * @package api
 * @subpackage objects
  */
class KalturaQuizUserEntry extends KalturaUserEntry{

	/**
	 * @var float
	 * @readonly
	 */
	public $score;

	private static $map_between_objects = array
	(
		"score"
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

	/* (non-PHPdoc)
	 * @see KalturaObject::toInsertableObject()
	 */
	public function toInsertableObject ( $object_to_fill = null , $props_to_skip = array() )
	{
		$object_to_fill = parent::toInsertableObject($object_to_fill, $props_to_skip);
		$isAnonymous = false;
		$anonKusers = kuserPeer::getKuserByPartnerAndUids(kCurrentContext::getCurrentPartnerId(), array('', 0));
		foreach ($anonKusers as $anonKuser)
		{
			if ($anonKuser->getKuserId() == $object_to_fill->getKuserId())
			{
				$isAnonymous = true;
			}
		}
		if (!$isAnonymous)
		{
			$c = new Criteria();
			$c->add(UserEntryPeer::KUSER_ID, $object_to_fill->getKuserId());
			$c->add(UserEntryPeer::ENTRY_ID, $this->entryId);
			$c->add(UserEntryPeer::TYPE, QuizPlugin::getCoreValue('UserEntryType', QuizUserEntryType::QUIZ));
			$userEntry = UserEntryPeer::doSelect($c);
			if (count($userEntry) > 0)
			{
				throw new KalturaAPIException(KalturaQuizErrors::QUIZ_USER_ENTRY_ALREADY_EXISTS, $this->entryId);
			}
		}
		return $object_to_fill;
	}

	public function validateForInsert($propertiesToSkip = array())
	{
		if(!QuizPlugin::isQuiz($this->entryId))
			throw new KalturaAPIException(KalturaQuizErrors::PROVIDED_ENTRY_IS_NOT_A_QUIZ, $this->entryId);
		parent::validateForInsert($propertiesToSkip);
	}

	protected function validateEntryId()
	{
		//do nothing - already validating in QuizPlugin::isQuiz
		return null;
	}

	protected function validateUserId()
	{
		//do nothing
		return null;
	}

}