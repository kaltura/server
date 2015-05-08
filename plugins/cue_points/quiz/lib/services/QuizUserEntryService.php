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

		if ($dbUserEntry->getType() != KalturaUserEntryType::KALTURA_QUIZ_USER_ENTRY)
		{
			throw new KalturaAPIException(KalturaErrors::INVALID_OBJECT_TYPE, $dbUserEntry->getType());
		}
		/**
		 * @var QuizUserEntry $dbUserEntry
		 */
		$score = $this->calculateScore();
		$dbUserEntry->setScore($score);
		$dbUserEntry->save();

		$userEntry = new KalturaQuizUserEntry();
		$userEntry->fromObject($dbUserEntry, $this->getResponseProfile());
		return $userEntry;
	}

	/**
	 * @return int
	 */
	protected function calculateScore()
	{

		return 95;
	}
}