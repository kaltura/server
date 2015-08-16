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
		$answerIds = $this->getAnswerIds();
		$questionType = QuizPlugin::getCuePointTypeCoreValue(QuizCuePointType::QUIZ_QUESTION);
		$questions = CuePointPeer::retrieveByEntryId($this->getEntryId(), array($questionType));
		$totalPoints = 0;
		$userPoints = 0;
		foreach ($questions as $question)
		{
			$optionalAnswers = $question->getOptionalAnswers();
			$currAnswer = null;
			if (isset($answerIds[$question->getId()]))
			{
				$currAnswer = CuePointPeer::retrieveByPK($answerIds[$question->getId()]);
			}
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