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

		if ($dbUserEntry->getType() != QuizUserEntryType::KALTURA_QUIZ_USER_ENTRY)
		{
			throw new KalturaAPIException(KalturaErrors::INVALID_OBJECT_TYPE, $dbUserEntry->getType());
		}
		/**
		 * @var QuizUserEntry $dbUserEntry
		 */
		$score = $this->calculateScore($dbUserEntry->getEntryId());
		$dbUserEntry->setScore($score);
		$dbUserEntry->setStatus(QuizUserEntryStatus::USER_ENTRY_STATUS_SUBMITTED);
		$dbUserEntry->save();

		$userEntry = new KalturaQuizUserEntry();
		$userEntry->fromObject($dbUserEntry, $this->getResponseProfile());
		return $userEntry;
	}

	/**
	 * @param $entryId
	 * @return int
	 */
	protected function calculateScore($entryId)
	{

		$finalScore = 0;
		$answerType = QuizPlugin::getCuePointTypeCoreValue(QuizCuePointType::ANSWER);
		$answers = CuePointPeer::retrieveByEntryId($entryId, array($answerType));
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
			/**
			 * @var kOptionalAnswer $chosenAnswer
			 */
			foreach ($optionalAnswers as $optionalAnswer)
			{
				/**
				 * @var kOptionalAnswer $optionalAnswer
				 */
				if ($optionalAnswer->getKey() === $answer->getAnswerKey())
				{
					if ($optionalAnswer->getCorrect())
					{
						$finalScore += $optionalAnswer->getWeight();
					}
				}
			}
		}

		return $finalScore;
	}
}