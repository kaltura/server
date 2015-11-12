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


		if ($kQuiz->getShowGradeAfterSubmission() || $this->getKuser()->getIsAdmin())
		{
			$userEntry->score = $dbUserEntry->calculateScore();
			$dbUserEntry->setScore($userEntry->score);
		}

		$dbUserEntry->save();

		return $userEntry;
	}
}
