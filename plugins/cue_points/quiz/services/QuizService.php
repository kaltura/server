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
	}

	/**
	 * Allows to add a quiz to an entry
	 *
	 * @action add
	 * @param string $entryId
	 * @param KalturaQuiz $quiz
	 * @return KalturaQuiz
	 * @throws KalturaErrors::ENTRY_ID_NOT_FOUND
	 * @throws KalturaErrors::INVALID_USER_ID
	 * @throws KalturaQuizErrors::PROVIDED_ENTRY_IS_ALREADY_A_QUIZ
	 */
	public function addAction( $entryId, KalturaQuiz $quiz )
	{
		$dbEntry = entryPeer::retrieveByPK($entryId);
		if (!$dbEntry)
			throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $entryId);

		if ( !is_null( QuizPlugin::getQuizData($dbEntry) ) )
			throw new KalturaAPIException(KalturaQuizErrors::PROVIDED_ENTRY_IS_ALREADY_A_QUIZ, $entryId);

		return $this->validateAndUpdateQuizData( $dbEntry, $quiz );
	}

	/**
	 * Allows to update a quiz
	 *
	 * @action update
	 * @param string $entryId
	 * @param KalturaQuiz $quiz
	 * @return KalturaQuiz
	 * @throws KalturaErrors::ENTRY_ID_NOT_FOUND
	 * @throws KalturaErrors::INVALID_USER_ID
	 * @throws KalturaQuizErrors::PROVIDED_ENTRY_IS_NOT_A_QUIZ
	 */
	public function updateAction( $entryId, KalturaQuiz $quiz )
	{
		$dbEntry = entryPeer::retrieveByPK($entryId);
		if (!$dbEntry)
			throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $entryId);

		if ( is_null( QuizPlugin::getQuizData($dbEntry) ) )
			throw new KalturaAPIException(KalturaQuizErrors::PROVIDED_ENTRY_IS_NOT_A_QUIZ, $entryId);

		return $this->validateAndUpdateQuizData( $dbEntry, $quiz );
	}

	private function validateAndUpdateQuizData( entry $dbEntry, KalturaQuiz $quiz )
	{
		$this->validateUserEntitledForUpdate( $dbEntry );
		$quizData = $quiz->toObject();
		QuizPlugin::setQuizData( $dbEntry, $quizData );
		$dbEntry->setIsTrimDisabled( true );
		$dbEntry->save();
		return $quiz;
	}

	/**
	 * Allows to get a quiz
	 *
	 * @action get
	 * @param string $entryId
	 * @return KalturaQuiz
	 * @throws KalturaErrors::ENTRY_ID_NOT_FOUND
	 * @throws KalturaQuizErrors::PROVIDED_ENTRY_IS_NOT_A_QUIZ
	 */
	public function getAction( $entryId )
	{
		$dbEntry = entryPeer::retrieveByPK($entryId);
		if (!$dbEntry)
			throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $entryId);

		$kQuiz = QuizPlugin::getQuizData($dbEntry);
		if ( is_null( $kQuiz ) )
			throw new KalturaAPIException(KalturaQuizErrors::PROVIDED_ENTRY_IS_NOT_A_QUIZ, $entryId);

		$quiz = new KalturaQuiz();
		$quiz->fromObject( $kQuiz );
		return $quiz;
	}

	/**
	 * List quiz objects by filter and pager
	 *
	 * @action list
	 * @param KalturaQuizFilter $filter
	 * @param KalturaFilterPager $pager
	 * @return KalturaQuizListResponse
	 */
	function listAction(KalturaQuizFilter $filter = null, KalturaFilterPager $pager = null)
	{
		if (!$filter)
			$filter = new KalturaQuizFilter;

		if (! $pager)
			$pager = new KalturaFilterPager ();

		return $filter->getListResponse($pager, $this->getResponseProfile());
	}

	/**
	 * Throws an error if the user is not owner or co-editor
	 *
	 * @param entry $dbEntry
	 */
	private function validateUserEntitledForUpdate(entry $dbEntry)
	{
		if ( kCurrentContext::$is_admin_session || kCurrentContext::getCurrentKsKuserId() == $dbEntry->getKuserId())
		return;

		$entitledKusers = explode(',', $dbEntry->getEntitledKusersEdit());
		if(!in_array(kCurrentContext::getCurrentKsKuserId(), $entitledKusers))
		{
			KalturaLog::debug('Update quiz allowed only with admin KS or entry owner or co-editor');
			throw new KalturaAPIException(KalturaErrors::INVALID_USER_ID);
		}
	}

}