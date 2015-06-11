<?php
/**
 * @package plugins.quiz
 * @subpackage model
 */

class AnswerCuePoint extends CuePoint
{
	const CUSTOM_DATA_QUIZ_USER_ENTRY_ID= 'quizUserEntryId';
	const CUSTOM_DATA_ANSWER_KEY = 'answerKey';
	const CUSTOM_DATA_IS_CORRECT = 'isCorrect';
	const CUSTOM_DATA_CORRECT_ANSWER_KEYS = 'correctAnswerKeys';
	const CUSTOM_DATA_EXPLANATION= 'explanation';

	public function __construct()
	{
		parent::__construct();
		$this->applyDefaultValues();
	}

	/**
	 * Applies default values to this object.
	 * This method should be called from the object's constructor (or equivalent initialization method).
	 * @see __construct()
	 */
	public function applyDefaultValues()
	{
		$this->setType(QuizPlugin::getCuePointTypeCoreValue(QuizCuePointType::QUIZ_ANSWER));
	}

	public function setQuizUserEntryId($v) {return $this->putInCustomData(self::CUSTOM_DATA_QUIZ_USER_ENTRY_ID, $v);}

	public function getQuizUserEntryId() {return $this->getFromCustomData(self::CUSTOM_DATA_QUIZ_USER_ENTRY_ID);}

	public function setAnswerKey($v) {return $this->putInCustomData(self::CUSTOM_DATA_ANSWER_KEY, $v);}

	public function getAnswerKey() {return $this->getFromCustomData(self::CUSTOM_DATA_ANSWER_KEY);}

	public function setIsCorrect($v) {return $this->putInCustomData(self::CUSTOM_DATA_IS_CORRECT, $v);}

	public function getIsCorrect() {return $this->getFromCustomData(self::CUSTOM_DATA_IS_CORRECT);}

	public function setCorrectAnswerKeys($v) {return $this->putInCustomData(self::CUSTOM_DATA_CORRECT_ANSWER_KEYS, $v);}

	public function getCorrectAnswerKeys() {return $this->getFromCustomData(self::CUSTOM_DATA_CORRECT_ANSWER_KEYS);}

	public function setExplanation($v) {return $this->putInCustomData(self::CUSTOM_DATA_EXPLANATION, $v);}

	public function getExplanation() {return $this->getFromCustomData(self::CUSTOM_DATA_EXPLANATION);}

	public function copyToClipEntry( entry $clipEntry, $clipStartTime, $clipDuration )
	{
		return false;
	}


	/* (non-PHPdoc)
 * @see BaseCuePoint::postInsert()
 */
	public function postInsert(PropelPDO $con = null)
	{
		parent::postInsert($con);


	}

	/**
	 * Code to be run after persisting the object
	 * @param PropelPDO $con
	 */
	public function postSave(PropelPDO $con = null)
	{
		kEventsManager::raiseEvent(new kObjectSavedEvent($this));
		$this->oldColumnsValues = array();
		$this->oldCustomDataValues = array();

		parent::postSave($con);
	}


}