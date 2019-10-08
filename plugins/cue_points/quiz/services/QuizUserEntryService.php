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
			throw new KalturaAPIException(KalturaErrors::INVALID_OBJECT_ID, $id);
		
		if ($dbUserEntry->getType() != QuizPlugin::getCoreValue('UserEntryType',QuizUserEntryType::QUIZ))
			throw new KalturaAPIException(KalturaQuizErrors::PROVIDED_ENTRY_IS_NOT_A_QUIZ, $id);
		
		$dbUserEntry->setStatus(QuizPlugin::getCoreValue('UserEntryStatus', QuizUserEntryStatus::QUIZ_SUBMITTED));
		$userEntry = new KalturaQuizUserEntry();
		$userEntry->fromObject($dbUserEntry, $this->getResponseProfile());
		$entryId = $dbUserEntry->getEntryId();
		$entry = entryPeer::retrieveByPK($entryId);
		if(!$entry)
			throw new KalturaAPIException(KalturaErrors::INVALID_OBJECT_ID, $entryId);
		
		$kQuiz = QuizPlugin::getQuizData($entry);
		if (!$kQuiz)
			throw new KalturaAPIException(KalturaQuizErrors::PROVIDED_ENTRY_IS_NOT_A_QUIZ, $entryId);
		
		list($score, $numOfCorrectAnswers) = $dbUserEntry->calculateScoreAndCorrectAnswers();
		$dbUserEntry->setScore($score);
		$dbUserEntry->setNumOfCorrectAnswers($numOfCorrectAnswers);	
		if ($kQuiz->getShowGradeAfterSubmission()== KalturaNullableBoolean::TRUE_VALUE || $this->getKs()->isAdmin() == true)
		{
			$userEntry->score = $score;
		}
		else
		{
			$userEntry->score = null;
		}

		$c = KalturaCriteria::create(CuePointPeer::OM_CLASS);
		$c->add(CuePointPeer::ENTRY_ID, $dbUserEntry->getEntryId(), Criteria::EQUAL);
		$c->add(CuePointPeer::TYPE, QuizPlugin::getCoreValue('CuePointType', QuizCuePointType::QUIZ_QUESTION));
		$questions = CuePointPeer::doSelect($c);
		$dbUserEntry->setNumOfQuestions(count($questions));
		$relevantQuestionCount = 0;
		foreach($questions as $question)
		{
			/* @var QuestionCuePoint $question*/
			if (!$question->getExcludeFromScore())
			{
				$relevantQuestionCount++;
			}
		}
		$dbUserEntry->setNumOfRelevnatQuestions($relevantQuestionCount);
		$dbUserEntry->setStatus(QuizPlugin::getCoreValue('UserEntryStatus', QuizUserEntryStatus::QUIZ_SUBMITTED));
		$dbUserEntry->save();
		self::calculateScoreByScoreType($kQuiz,$userEntry, $dbUserEntry, $score);

		return $userEntry;
	}

	protected function calculateScoreByScoreType($kQuiz, $kalturaUserEntry, $dbUserEntry, $currentScore)
	{
		if ($dbUserEntry->getVersion() == 0)
		{
			$calculatedScore = $currentScore;
		}
		else
		{
			$scoreType = $kQuiz->getScoreType();
			//retrieve user entry list order by version desc
			$userEntryVersions = UserEntryPeer::retriveUserEntriesSubmitted($dbUserEntry->getKuserId(), $dbUserEntry->getEntryId(), QuizPlugin::getCoreValue('UserEntryType', QuizUserEntryType::QUIZ));
			switch ($scoreType)
			{
				case KalturaScoreType::HIGHEST:
					$calculatedScore = self::getHighestScore($userEntryVersions);
					break;

				case KalturaScoreType::LOWEST:
					$calculatedScore = self::getLowestScore($userEntryVersions);
					break;

				case KalturaScoreType::LATEST:
					$calculatedScore = reset($userEntryVersions)->getScore();
					break;

				case KalturaScoreType::FIRST:
					$calculatedScore = end($userEntryVersions)->getScore();
					break;

				case KalturaScoreType::AVERAGE:
					$calculatedScore = self::getAverageScore($userEntryVersions);
					break;
			}
		}

		$dbUserEntry->setCalculatedScore($calculatedScore);
		$dbUserEntry->save();
		if ($kQuiz->getShowGradeAfterSubmission()== KalturaNullableBoolean::TRUE_VALUE || $this->getKs()->isAdmin() == true)
		{
			$kalturaUserEntry->calculatedScore = $calculatedScore;
		}
	}

	protected function getHighestScore($userEntryVersions)
	{
		$highest =  reset($userEntryVersions)->getScore();
		foreach ($userEntryVersions as $userEntry)
		{
			if ($userEntry->getScore() > $highest)
			{
				$highest = $userEntry->getScore();
			}
		}
		return $highest;
	}

	protected function getLowestScore($userEntryVersions)
	{
		$lowest =  reset($userEntryVersions)->getScore();
		foreach ($userEntryVersions as $userEntry)
		{
			if ($userEntry->getScore() < $lowest)
			{
				$lowest = $userEntry->getScore();
			}
		}
		return $lowest;
	}

	protected function getAverageScore($userEntryVersions)
	{
		$sumScores = 0;
		foreach ($userEntryVersions as $userEntry)
		{
			$sumScores += $userEntry->getScore();
		}
		$calculatedScore = floatval($sumScores / count($userEntryVersions));
		return $calculatedScore;
	}
}
