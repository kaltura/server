<?php
/**
 * Allows user to get analytics regarding quizes
 *
 * @service quizAnalytics
 * @package plugins.quiz
 * @subpackage api.services
 */

class QuizAnalyticsService extends KalturaBaseService
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
	public function getAverageQuizScore($entryId)
	{
		$dbEntry = entryPeer::retrieveByPK($entryId);
		if (!$dbEntry)
			throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $entryId);
		/**
		 * @var kQuiz $kQuiz
		 */
		$kQuiz = QuizPlugin::validateAndGetQuiz( $dbEntry );
		$c = new Criteria();
		$c->add(UserEntryPeer::ENTRY_ID, $entryId);
		$c->add(UserEntryPeer::TYPE, QuizUserEntryType::KALTURA_QUIZ_USER_ENTRY);
		$c->add(UserEntryPeer::STATUS, QuizUserEntryStatus::USER_ENTRY_STATUS_SUBMITTED);
		$quizzes = UserEntryPeer::doSelect($c);
		$sumOfScores = 0;
		foreach ($quizzes as $quiz)
		{
			/**
			 * @var QuizUserEntry $quiz
			 */
				$sumOfScores += $quiz->getScore();
		}
		$ans = $sumOfScores/(count($quizzes));
		return $ans;

//		return 85.0;

	}

	/**
	 *
	 * @action getAverageQuestionScore
	 * @param string $questionId
	 * @return float
	 * @throws KalturaAPIException
	 */
	public function getAverageQuestionScore($questionId)
	{
		$dbCuePoint = CuePointPeer::retrieveByPK( $questionId );

		if(!$dbCuePoint)
			throw new KalturaAPIException(KalturaCuePointErrors::INVALID_CUE_POINT_ID, $id);

		return 15.0;
	}


	/**
	 *
	 * @action getAverageUserScore
	 * @param string $userId
	 * @return float
	 * @throws KalturaAPIException
	 */
	public function getAverageUserScore($userId)
	{
		if (is_null($userId) || $userId == '')
		{
			$userId = kCurrentContext::$ks_uid;
		}

		if (!kCurrentContext::$is_admin_session && kCurrentContext::$ks_uid != $userId)
			throw new KalturaAPIException(KalturaErrors::CANNOT_RETRIEVE_ANOTHER_USER_USING_NON_ADMIN_SESSION, $userId);


		return 50.0;
	}
}