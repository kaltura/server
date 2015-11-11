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
		{
			throw new KalturaAPIException(KalturaErrors::INVALID_OBJECT_TYPE, $dbUserEntry->getType());
		}
		/**
		 * @var QuizUserEntry $dbUserEntry
		 */
		list($score, $numOfCorrectAnswers) = $dbUserEntry->calculateScoreAndCorrectAnswers();
		$dbUserEntry->setScore($score);
		$dbUserEntry->setNumOfCorrectAnswers($numOfCorrectAnswers);

		$c = new Criteria();
		$c->add(CuePointPeer::ENTRY_ID, $dbUserEntry->getEntryId(), Criteria::EQUAL);
		$c->add(CuePointPeer::TYPE, QuizPlugin::getCoreValue('CuePointType', QuizCuePointType::QUIZ_QUESTION));
		$dbUserEntry->setNumOfQuestions(CuePointPeer::doCount($c));

		$dbUserEntry->setStatus(QuizPlugin::getCoreValue('UserEntryStatus', QuizUserEntryStatus::QUIZ_SUBMITTED));
		$dbUserEntry->save();

		$userEntry = new KalturaQuizUserEntry();
		$userEntry->fromObject($dbUserEntry, $this->getResponseProfile());
		return $userEntry;
	}
}