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
	public function addAnswerId($questionId, $answerId)
	{
		$answerIds = $this->getAnswerIds();
		$answerIds[$questionId] = $answerId;
		$this->putInCustomData("answerIds", $answerIds);
	}
	public function getAnswerIds(){return $this->getFromCustomData("answerIds", null, array());}
	
	public function calculateScore()
	{
		//TODO when we have indexing of CuePoints in the sphinx we don't need the answerIds in the custom_data since we can just get the answers of the specific userEntry
		$answerIds = $this->getAnswerIds();
		$questionType = QuizPlugin::getCuePointTypeCoreValue(QuizCuePointType::QUIZ_QUESTION);
		$questions = CuePointPeer::retrieveByEntryId($this->getEntryId(), array($questionType));
		$totalPoints = 0;
		$userPoints = 0;
		foreach ($questions as $question)
		{
			$optionalAnswers = $question->getOptionalAnswers();
			$answers = array();
			if (isset($answerIds[$question->getId()]))
			{
				$answerId = $answerIds[$question->getId()];
				//TODO change to retrieveByPks (multiple, only one query, no need for multiple)
				$currAnswer = CuePointPeer::retrieveByPK($answerId);
				$answers[] = $currAnswer;
			}
			list($totalForQuestion, $userPointsForQuestion) = $this->getCorrectAnswerWeight($optionalAnswers, $answers);
			$totalPoints += $totalForQuestion;
			$userPoints += $userPointsForQuestion;
		}
		return $totalPoints?($userPoints/$totalPoints):0;
	}

	/**
	 * @param $optionalAnswers
	 * @param $answers
	 * @return array
	 */
	protected function getCorrectAnswerWeight($optionalAnswers, $answers)
	{
		$totalPoints = 0;
		$userPoints = 0;
		foreach ($optionalAnswers as $optionalAnswer)
		{
			/**
			 * @var kOptionalAnswer $optionalAnswer
			 */
			if ($optionalAnswer->getIsCorrect())
			{
				$totalPoints += $optionalAnswer->getWeight();
				foreach ($answers as $currAnswer)
				{
					if ($optionalAnswer->getKey() == $currAnswer->getAnswerKey())
					{
						$userPoints += $optionalAnswer->getWeight();
					}
				}
			}
		}
		return array($totalPoints, $userPoints);
	}

}