<?php
/**
 * @package plugins.quiz
 * @subpackage model
 */

class QuestionCuePoint extends CuePoint implements IMetadataObject
{
	const CUSTOM_DATA_OPTIONAL_ANSWERS = 'optionalAnswers';
	const CUSTOM_DATA_HINT = 'hint';
	const CUSTOM_DATA_CORRECT_ANSWER_KEYS = 'correctAnswerKeys';
	const CUSTOM_DATA_EXPLANATION = 'explanation';

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
		$this->setType(QuizPlugin::getCuePointTypeCoreValue(QuizCuePointType::QUIZ_QUESTION));
	}

	public function setOptionalAnswers($v) {return $this->putInCustomData(self::CUSTOM_DATA_OPTIONAL_ANSWERS, $v);}

	public function getOptionalAnswers() {return $this->getFromCustomData(self::CUSTOM_DATA_OPTIONAL_ANSWERS, null, array());}

	public function setHint($v) {return $this->putInCustomData(self::CUSTOM_DATA_HINT, $v);}

	public function getHint() {return $this->getFromCustomData(self::CUSTOM_DATA_HINT);}

	public function setExplanation($v) {return $this->putInCustomData(self::CUSTOM_DATA_EXPLANATION, $v);}

	public function getExplanation() {return $this->getFromCustomData(self::CUSTOM_DATA_EXPLANATION);}

	public function getIsPublic()	{return true;}

	public function getMetadataObjectType()
	{
		return QuizPlugin::getCoreValue('MetadataObjectType', QuizCuePointMetadataObjectType::QUESTION_CUE_POINT);
	}

	public function shouldReIndexEntry(array $modifiedColumns = array())
	{
		return false;
	}
}