<?php

/**
 *
 * @service quizUserEntry
 * @package plugins.quiz
 * @subpackage api.services
 */
class QuizUserEntryService extends KalturaBaseService{

	/**
	 * Submits the quiz so that it's status will be submitted and calculates the score for the quiz
	 *
	 * @action submitQuiz
	 * @actionAlias userEntry.submitQuiz
	 * @param int $id
	 * @return KalturaQuizUserEntry
	 * @throws KalturaAPIException
	 */
	public function submitQuizAction($id)
	{
		$dbUserEntry = UserEntryPeer::retrieveByPK($id);
		if (!$dbUserEntry)
		{
			throw new KalturaAPIException(KalturaErrors::INVALID_OBJECT_ID, $id);
		}
		
		if ($dbUserEntry->getType() != QuizPlugin::getCoreValue('UserEntryType',QuizUserEntryType::QUIZ))
		{
			throw new KalturaAPIException(KalturaQuizErrors::PROVIDED_ENTRY_IS_NOT_A_QUIZ, $id);
		}

		if ($dbUserEntry->getStatus() == QuizPlugin::getCoreValue('UserEntryStatus', QuizUserEntryStatus::QUIZ_SUBMITTED))
		{
			throw new KalturaAPIException(KalturaQuizErrors::USER_ENTRY_QUIZ_ALREADY_SUBMITTED);
		}

		$dbUserEntry->setStatus(QuizPlugin::getCoreValue('UserEntryStatus', QuizUserEntryStatus::QUIZ_SUBMITTED));
		$userEntry = new KalturaQuizUserEntry();
		$userEntry->fromObject($dbUserEntry, $this->getResponseProfile());
		$entryId = $dbUserEntry->getEntryId();
		$entry = entryPeer::retrieveByPK($entryId);
		if(!$entry)
		{
			throw new KalturaAPIException(KalturaErrors::INVALID_OBJECT_ID, $entryId);
		}
		
		$kQuiz = QuizPlugin::getQuizData($entry);
		if (!$kQuiz)
		{
			throw new KalturaAPIException(KalturaQuizErrors::PROVIDED_ENTRY_IS_NOT_A_QUIZ, $entryId);
		}
		
		list($score, $numOfCorrectAnswers) = $dbUserEntry->calculateScoreAndCorrectAnswers();
		$dbUserEntry->setScore($score);
		$dbUserEntry->setBestScore(max($dbUserEntry->getScore(),$dbUserEntry->getBestScore()));
		$dbUserEntry->setNumOfCorrectAnswers($numOfCorrectAnswers);	
		if ($kQuiz->getShowGradeAfterSubmission()== KalturaNullableBoolean::TRUE_VALUE || $this->getKs()->isAdmin() == true)
		{
			$userEntry->score = $score;
			$userEntry->bestScore = $dbUserEntry->getBestScore();
			$userEntry->previousScore = $dbUserEntry->getPreviousScore();
		}
		else
		{
			$userEntry->score = null;
			$userEntry->bestScore = null;
			$userEntry->previousScore = null;
		}

		$c = new Criteria();
		$c->add(CuePointPeer::ENTRY_ID, $dbUserEntry->getEntryId(), Criteria::EQUAL);
		$c->add(CuePointPeer::TYPE, QuizPlugin::getCoreValue('CuePointType', QuizCuePointType::QUIZ_QUESTION));
		$dbUserEntry->setNumOfQuestions(CuePointPeer::doCount($c));
		$dbUserEntry->setStatus(QuizPlugin::getCoreValue('UserEntryStatus', QuizUserEntryStatus::QUIZ_SUBMITTED));

		$dbUserEntry->save();

		return $userEntry;
	}

	/**
	 * retake the quiz if have retakes left
	 *
	 * @action retakeQuiz
	 * @actionAlias userEntry.retakeQuiz
	 * @param int $id
	 * @return KalturaQuizUserEntry
	 * @throws KalturaAPIException
	 */
	public function retakeQuizAction($id)
	{
		$dbUserEntry = UserEntryPeer::retrieveByPK($id);
		if (!$dbUserEntry)
		{
			throw new KalturaAPIException(KalturaErrors::INVALID_OBJECT_ID, $id);
		}

		if ($dbUserEntry->getType() != QuizPlugin::getCoreValue('UserEntryType',QuizUserEntryType::QUIZ))
		{
			throw new KalturaAPIException(KalturaQuizErrors::PROVIDED_ENTRY_IS_NOT_A_QUIZ, $id);
		}

		if ($dbUserEntry->getStatus() != QuizPlugin::getCoreValue('UserEntryStatus', QuizUserEntryStatus::QUIZ_SUBMITTED))
		{
			throw new KalturaAPIException(KalturaQuizErrors::USER_ENTRY_QUIZ_ALREADY_RETAKEN);
		}

		$entryId = $dbUserEntry->getEntryId();
		$entry = entryPeer::retrieveByPK($entryId);
		if(!$entry)
		{
			throw new KalturaAPIException(KalturaErrors::INVALID_OBJECT_ID, $entryId);
		}

		$kQuiz = QuizPlugin::getQuizData($entry);
		if (!$kQuiz)
		{
			throw new KalturaAPIException(KalturaQuizErrors::PROVIDED_ENTRY_IS_NOT_A_QUIZ, $entryId);
		}

		$retakesLeft = $dbUserEntry->getNumOfRetakesAllowed();
		if ($retakesLeft < 1)
		{
			throw new KalturaAPIException(KalturaQuizErrors::NO_RETAKES_LEFT, $id);
		}

		$dbUserEntry->setNumOfRetakesAllowed($retakesLeft -1);
		$dbUserEntry->setPreviousScore($dbUserEntry->getScore());
		$dbUserEntry->setScore(0);
		$dbUserEntry->setStatus(UserEntryStatus::ACTIVE);
		$userEntry = new KalturaQuizUserEntry();
		$userEntry->fromObject($dbUserEntry, $this->getResponseProfile());
		$dbUserEntry->save();

		return $userEntry;
	}
}
