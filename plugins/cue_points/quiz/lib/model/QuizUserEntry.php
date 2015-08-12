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
	 * @return int
	 */
	public function calculateScore()
	{
		$finalScore = 0;
		$questionType = QuizPlugin::getCuePointTypeCoreValue(QuizCuePointType::QUIZ_QUESTION);
		$questions = CuePointPeer::retrieveByEntryId($this->getEntryId(), array($questionType));
		$totalPoints = 0;
		$userPoints = 0;
		foreach ($questions as $question)
		{
			/**
			 * @var QuestionCuePoint $question
			 */
			$answersCrit = new Criteria();
			$answersCrit->add(CuePointPeer::ENTRY_ID,$this->getEntryId());
			$answersCrit->add(CuePointPeer::PARENT_ID, $question->getId());
			$answers = CuePointPeer::doSelect($answersCrit);
			$currAnswer = null;
			foreach ($answers as $answer)
			{
				/**
				 * @var AnswerCuePoint $answer
				 */
				if ($answer->getQuizUserEntryId() == $this->id)
				{
					$currAnswer = $answer;
					break;
				}
			}
			$optionalAnswers = $question->getOptionalAnswers();
			foreach ($optionalAnswers as $optionalAnswer)
			{
				/**
				 * @var kOptionalAnswer $optionalAnswer
				 */
				if ($optionalAnswer->getIsCorrect())
				{
					$totalPoints += $optionalAnswer->getWeight();
					if ($currAnswer && ($optionalAnswer->getKey() == $currAnswer->getAnswerKey()) )
					{
						$userPoints += $optionalAnswer->getWeight();
					}
				}
			}
		}
		return $totalPoints?($userPoints/$totalPoints):0;
	}

}