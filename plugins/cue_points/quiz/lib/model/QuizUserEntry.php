<?php

/**
 * @package plugins.quiz
 * @subpackage model
 */
class QuizUserEntry extends UserEntry{

	const QUIZ_OM_CLASS = 'QuizUserEntry';
	/**
	 * @var float
	 */
	protected $score;

	/**
	 * @var int
	 */
	protected $numOfQuestions;

	/**
	 * @var int
	 */
	protected $numOfCorrectAnswers;


	public function applyDefaultValues()
	{
		parent::applyDefaultValues();
		$this->setType(QuizPlugin::getCoreValue('UserEntryType' , QuizUserEntryType::QUIZ));
	}


	public function setScore($v){ $this->putInCustomData("score", $v);}
	public function getScore(){ return $this->getFromCustomData("score");}
	public function setNumOfQuestions($v){ $this->putInCustomData("numOfQuestions", $v);}
	public function getNumOfQuestions(){ return $this->getFromCustomData("numOfQuestions");}
	public function setNumOfCorrectAnswers($v){ $this->putInCustomData("numOfCorrectAnswers", $v);}
	public function getNumOfCorrectAnswers(){ return $this->getFromCustomData("numOfCorrectAnswers");}
	public function addAnswerId($questionId, $answerId)
	{
		$answerIds = $this->getAnswerIds();
		$answerIds[$questionId] = $answerId;
		$this->putInCustomData("answerIds", $answerIds);
	}
	public function getAnswerIds(){return $this->getFromCustomData("answerIds", null, array());}
	
	public function calculateScoreAndCorrectAnswers()
	{
		//TODO when we have indexing of CuePoints in the sphinx we don't need the answerIds in the custom_data since we can just get the answers of the specific userEntry
		$answerIds = $this->getAnswerIds();
		$questionType = QuizPlugin::getCuePointTypeCoreValue(QuizCuePointType::QUIZ_QUESTION);
		$questions = CuePointPeer::retrieveByEntryId($this->getEntryId(), array($questionType));
		$totalPoints = 0;
		$userPoints = 0;
		$numOfCorrectAnswers = 0;
		foreach ($questions as $question)
		{
			$optionalAnswers = $question->getOptionalAnswers();
			$answers = array();
			if (isset($answerIds[$question->getId()]))
			{
				$answerId = $answerIds[$question->getId()];
				//TODO change to retrieveByPks (multiple, only one query, no need for multiple)
				$currAnswer = CuePointPeer::retrieveByPK($answerId);
				if ( $currAnswer->getIsCorrect() )
					$numOfCorrectAnswers++;
				$answers[] = $currAnswer;
			}
			list($totalForQuestion, $userPointsForQuestion) = $this->getCorrectAnswerWeight($optionalAnswers, $answers);
			$totalPoints += $totalForQuestion;
			$userPoints += $userPointsForQuestion;
		}
		$score = $totalPoints?($userPoints/$totalPoints):0;
		return array( $score, $numOfCorrectAnswers );
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

	public function checkAlreadyExists()
	{
		$isAnonymous = false;
		$anonKusers = kuserPeer::getKuserByPartnerAndUids(kCurrentContext::getCurrentPartnerId(), array('', 0));
		foreach ($anonKusers as $anonKuser)
		{
			if ($anonKuser->getKuserId() == $this->getKuserId())
			{
				$isAnonymous = true;
			}
		}
		if (!$isAnonymous)
		{
			$c = new Criteria();
			$c->add(UserEntryPeer::KUSER_ID, $this->getKuserId());
			$c->add(UserEntryPeer::ENTRY_ID, $this->getEntryId());
			$c->add(UserEntryPeer::TYPE, QuizPlugin::getCoreValue('UserEntryType', QuizUserEntryType::QUIZ));
			
			return UserEntryPeer::doSelectOne($c);
		}
		
		return false;
	}

}
