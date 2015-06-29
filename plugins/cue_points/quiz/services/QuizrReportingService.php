<?php
/**
 * Allows user to get reports regarding quizes
 *
 * @service quizReports
 * @package plugins.quiz
 * @subpackage api.services
 */

class QuizrReportingService extends KalturaBaseService
{

	public function initService($serviceId, $serviceName, $actionName)
	{
		parent::initService($serviceId, $serviceName, $actionName);

		if(!QuizPlugin::isAllowedPartner($this->getPartnerId()))
		{
			throw new KalturaAPIException(KalturaErrors::FEATURE_FORBIDDEN, QuizPlugin::PLUGIN_NAME);
		}
	}

	/**
	 * get average score of all Quiz session on a specific quiz
	 *
	 * @action getAverageQuizScore
	 * @param string $entryId
	 * @return float
	 * @throws KalturaAPIException
	 */
	public function getAverageScoreAction($entryId)
	{
		$ans = -1;
		$dbEntry = entryPeer::retrieveByPK($entryId);
		if (!$dbEntry)
			throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $entryId);
		/**
		 * @var kQuiz $kQuiz
		 */
		$kQuiz = QuizPlugin::validateAndGetQuiz( $dbEntry );
		$c = new Criteria();
		$c->add(UserEntryPeer::ENTRY_ID, $entryId);
		$c->add(UserEntryPeer::TYPE, QuizPlugin::getCoreValue('UserEntryType',QuizUserEntryType::QUIZ));
		$c->add(UserEntryPeer::STATUS, QuizPlugin::getCoreValue('UserEntryStatus', QuizUserEntryStatus::QUIZ_SUBMITTED));
		$quizzes = UserEntryPeer::doSelect($c);
		$numOfQuizzesFound = count($quizzes);
		KalturaLog::debug("Found $numOfQuizzesFound quizzes that were submitted");
		if ($numOfQuizzesFound)
		{
			$sumOfScores = 0;
			foreach ($quizzes as $quiz)
			{
				/**
				 * @var QuizUserEntry $quiz
				 */
				$sumOfScores += $quiz->getScore();
			}
			$ans = $sumOfScores / $numOfQuizzesFound;
		}
		return $ans;
	}

	/**
	 * @action listQuestionsSummary
	 * @param string $entryId
	 * @return KalturaQuestionSummaryArray
	 * @throws KalturaAPIException
	 */
	public function listQuestionsSummaryAction($entryId)
	{
		$ans = new KalturaQuestionSummaryArray();
		$dbEntry = entryPeer::retrieveByPK($entryId);
		if (!$dbEntry)
			throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $entryId);
		/**
		 * @var kQuiz $kQuiz
		 */
		$kQuiz = QuizPlugin::validateAndGetQuiz( $dbEntry );
		$c = new Criteria();
		$c->add(CuePointPeer::ENTRY_ID, $entryId);
		$c->add(CuePointPeer::TYPE, QuizPlugin::getCoreValue('CuePointType',QuizCuePointType::QUIZ_QUESTION));
		$questions = CuePointPeer::doSelect($c);
		foreach ($questions as $question)
		{
			$numOfCorrectAnswers = 0;
			/**
			 * @var QuestionCuePoint $question
			 */
			$c = new Criteria();
			$c->add(CuePointPeer::ENTRY_ID, $entryId);
			$c->add(CuePointPeer::TYPE, QuizPlugin::getCoreValue('CuePointType',QuizCuePointType::QUIZ_ANSWER));
			$c->add(CuePointPeer::PARENT_ID, $question->getId());
			$answers = CuePointPeer::doSelect($c);
			$numOfAnswers = count($answers);
			if ($numOfAnswers)
			{
				foreach ($answers as $answer)
				{
					/**
					 * @var AnswerCuePoint $answer
					 */
					$optionalAnswers = $question->getOptionalAnswers();
					$correct = false;
					foreach ($optionalAnswers as $optionalAnswer)
					{
						/**
						 * @var kOptionalAnswer $optionalAnswer
						 */
						if ($optionalAnswer->getKey() === $answer->getAnswerKey())
						{
							if ($optionalAnswer->getIsCorrect())
							{
								$numOfCorrectAnswers++;
								break;
							}
						}
					}
				}
				$pctg = $numOfCorrectAnswers/$numOfAnswers;
			}
			else
			{
				$pctg = 0.0;
			}
			$ans[$question->getId()] = $pctg*100;
		}
		return $ans;
	}

}