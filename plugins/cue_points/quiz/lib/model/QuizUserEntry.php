<?php

/**
 * @package Core
 * @subpackage model
 */
class QuizUserEntry extends UserEntry{

	const QUIZ_OM_CLASS = 'QuizUserEntry';
	/**
	 * @var int
	 */
	protected $score;

	public function applyDefaultValues()
	{
		parent::applyDefaultValues();
		$this->setType(QuizPlugin::getCoreValue('UserEntryType' , QuizUserEntryType::QUIZ));
	}


	public function setScore($v){ $this->putInCustomData("score", $v);}
	public function getScore(){ return $this->getFromCustomData("score");}

	/**
	 * @param $entryId
	 * @return int
	 */
	public function calculateScore()
	{
		$finalScore = 0;
		$answerType = QuizPlugin::getCuePointTypeCoreValue(QuizCuePointType::QUIZ_ANSWER);
		$answers = CuePointPeer::retrieveByEntryId($this->getEntryId(), array($answerType));
		foreach ($answers as $answer)
		{
			/**
			 * @var AnswerCuePoint $answer
			 */
			$question = CuePointPeer::retrieveByPK($answer->getParentId());
			/**
			 * @var QuestionCuePoint $question
			 */
			$optionalAnswers = $question->getOptionalAnswers();
			foreach ($optionalAnswers as $optionalAnswer)
			{
				/**
				 * @var kOptionalAnswer $optionalAnswer
				 */
				if ($optionalAnswer->getKey() === $answer->getAnswerKey())
				{
					if ($optionalAnswer->getIsCorrect())
					{
						$finalScore += $optionalAnswer->getWeight();
					}
				}
			}
		}
		return $finalScore;
	}

}