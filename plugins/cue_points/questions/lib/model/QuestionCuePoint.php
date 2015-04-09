<?php
/**
 * @package plugins.questions
 * @subpackage model
 */

class QuestionCuePoint extends CuePoint //TODO: implements IMetadataObject
{
	const CUSTOM_DATA_OPTIONAL_ANSWERS = 'optionalAnswers';
	const CUSTOM_DATA_HINT = 'hint';
	const CUSTOM_DATA_CORRECT_ANSWERS_KEYS = 'correctAnswersKeys';
	const CUSTOM_DATA_CORRECT_ANSWER_EXPLANATION = 'correctAnswerExplanation';

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
		$this->setType(questionsPlugin::getCuePointTypeCoreValue(QuestionsCuePointType::QUESTION));
	}

	public function setOptionalAnswers($v) {return $this->putInCustomData(self::CUSTOM_DATA_OPTIONAL_ANSWERS, $v);}

	public function getOptionalAnswers() {return $this->getFromCustomData(self::CUSTOM_DATA_OPTIONAL_ANSWERS, null, array());}

	public function setHint($v) {return $this->putInCustomData(self::CUSTOM_DATA_HINT, $v);}

	public function getHint() {return $this->getFromCustomData(self::CUSTOM_DATA_OPTIONAL_ANSWERS);}

	public function setCorrectAnswerExplanation($v) {return $this->putInCustomData(self::CUSTOM_DATA_CORRECT_ANSWER_EXPLANATION, $v);}

	public function getCorrectAnswerExplanation() {return $this->getFromCustomData(self::CUSTOM_DATA_CORRECT_ANSWER_EXPLANATION);}

	public function setCorrectAnswersKeys($v) {return $this->putInCustomData(self::CUSTOM_DATA_CORRECT_ANSWERS_KEYS, $v);}

	public function getCorrectAnswersKeys() {return $this->getFromCustomData(self::CUSTOM_DATA_CORRECT_ANSWERS_KEYS, null, array() );}

}