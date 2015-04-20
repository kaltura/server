<?php
/**
 * Allows user to handle quizzes
 *
 * @service quiz
 * @package plugins.quiz
 * @subpackage api.services
 */

class QuizService extends KalturaBaseService
{

	public function initService($serviceId, $serviceName, $actionName)
	{
		parent::initService($serviceId, $serviceName, $actionName);

		if(!QuizPlugin::isAllowedPartner($this->getPartnerId()))
		{
			throw new KalturaAPIException(KalturaErrors::FEATURE_FORBIDDEN, QuizPlugin::PLUGIN_NAME);
		}

		 //TODO interesting
		/*if (!kCurrentContext::$ks_uid || kCurrentContext::$ks_uid == "")
		{
			throw new KalturaAPIException(KalturaErrors::INVALID_USER_ID);
		}*/
	}

	/**
	 * Allows to add a quiz to an entry
	 *
	 * @action add
	 * @param string $entryId
	 * @param KalturaQuiz $quiz
	 * @return KalturaQuiz
	 */
	public function addAction( $entryId, KalturaQuiz $quiz )
	{
		$dbEntry = entryPeer::retrieveByPK($entryId);
		if (!$dbEntry)
			throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $entryId);

		if ( !is_null( $this->getQuizData($dbEntry) ) )
			throw new KalturaAPIException(KalturaQuizErrors::PROVIDED_ENTRY_IS_ALREADY_A_QUIZ, $entryId);

		$quizData = $quiz->toObject();
		$this->setQuizData( $dbEntry, $quizData );
		$dbEntry->save();

		return $quiz;
		//TODO how to verify user can update this entry???
	}

	/**
	 * Allows to update a quiz
	 *
	 * @action update
	 * @param string $entryId
	 * @param KalturaQuiz $quiz
	 * @return KalturaQuiz
	 */
	public function updateAction( $entryId, KalturaQuiz $quiz )
	{
		$dbEntry = entryPeer::retrieveByPK($entryId);
		if (!$dbEntry)
			throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $entryId);

		if ( is_null( $this->getQuizData($dbEntry) ) )
			throw new KalturaAPIException(KalturaQuizErrors::PROVIDED_ENTRY_IS_NOT_A_QUIZ, $entryId);

		$quizData = $quiz->toObject();
		$this->setQuizData( $dbEntry, $quizData );
		$dbEntry->save();
		return $quiz;
		//TODO how to verify user can update this entry???
	}

	/**
	 * Allows to get a quiz
	 *
	 * @action get
	 * @param string $entryId
	 * @return KalturaQuiz
	 */
	public function getAction( $entryId )
	{
		$dbEntry = entryPeer::retrieveByPK($entryId);
		if (!$dbEntry)
			throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $entryId);

		$kQuiz = $this->getQuizData($dbEntry);
		if ( is_null( $kQuiz ) )
			throw new KalturaAPIException(KalturaQuizErrors::PROVIDED_ENTRY_IS_NOT_A_QUIZ, $entryId);

		$quiz = new KalturaQuiz();
		$quiz = $quiz->fromObject( $kQuiz );
		return $quiz;
	}

	private function getQuizData( entry $entry )
	{
		$quizData = $entry->getFromCustomData( QuizPlugin::QUIZ_DATA );

		if($quizData)
			$quizData = unserialize($quizData);

		return $quizData;
	}

	private function setQuizData( entry $entry, kQuiz $kQuiz )
	{
		$entry->putInCustomData( QuizPlugin::QUIZ_DATA, serialize($kQuiz) );
	}

}