<?php
/**
 * @package plugins.questionAnswer
 * @subpackage model
 */

class QuestionCuePoint extends CuePoint //TODO: implements IMetadataObject
{
	const CUSTOM_DATA_OPTIONAL_ANSWERS = 'optionalAnswers';
	const CUSTOM_DATA_HINT = 'hint';
	const CUSTOM_DATA_CORRECT_ANSWERS_KEYS = 'correctAnswersKeys';
	const ANSWER_EXPLANATION = 'answerExplanation';

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
		$this->setType(QuestionAnswerPlugin::getCuePointTypeCoreValue(QuestionAnswerCuePointType::QUESTION));
	}

	public function setOptionalAnswers($v) {return $this->putInCustomData(self::CUSTOM_DATA_OPTIONAL_ANSWERS, $v);}

	public function getOptionalAnswers() {return $this->getFromCustomData(self::CUSTOM_DATA_OPTIONAL_ANSWERS, null, array());}

	public function setHint($v) {return $this->putInCustomData(self::CUSTOM_DATA_HINT, $v);}

	public function getHint() {return $this->getFromCustomData(self::CUSTOM_DATA_OPTIONAL_ANSWERS);}

	public function setAnswerExplanation($v) {return $this->putInCustomData(self::ANSWER_EXPLANATION, $v);}

	public function getAnswerExplanation() {return $this->getFromCustomData(self::ANSWER_EXPLANATION);}

	public function setCorrectAnswersKeys($v) {return $this->putInCustomData(self::CUSTOM_DATA_CORRECT_ANSWERS_KEYS, $v);}

	public function getCorrectAnswersKeys() {return $this->getFromCustomData(self::CUSTOM_DATA_CORRECT_ANSWERS_KEYS, null, array() );}

}