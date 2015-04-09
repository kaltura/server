<?php
/**
 * @package plugins.questions
 * @subpackage model
 */

class AnswerCuePoint extends CuePoint //TODO: implements IMetadataObject
{
	const CUSTOM_DATA_ANSWERS_USER_ENTRY_ID= 'answersUserEntryId';
	const CUSTOM_DATA_ANSWER_KEY = 'answerKey';

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
		$this->setType(questionsPlugin::getCuePointTypeCoreValue(QuestionsCuePointType::ANSWER));
	}

	public function setAnswersUserEntryId($v) {return $this->putInCustomData(self::CUSTOM_DATA_ANSWERS_USER_ENTRY_ID, $v);}

	public function getAnswersUserEntryId() {return $this->getFromCustomData(self::CUSTOM_DATA_ANSWERS_USER_ENTRY_ID);}

	public function setAnswerKey($v) {return $this->putInCustomData(self::CUSTOM_DATA_ANSWER_KEY, $v);}

	public function getAnswerKey() {return $this->getFromCustomData(self::CUSTOM_DATA_ANSWER_KEY);}

}