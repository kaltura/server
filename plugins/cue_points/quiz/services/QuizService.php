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
		$kQuiz = QuizPlugin::validateAndGetQuiz( $dbEntry );
		return $this->validateAndUpdateQuizData( $dbEntry, $quiz, $kQuiz->getVersion(), $kQuiz );
	}

	/**
	 * if user is entitled for this action will update quizData on entry
	 * @param entry $dbEntry
	 * @param KalturaQuiz $quiz
	 * @param int $currentVersion
	 * @param kQuiz|null $newQuiz
	 * @return KalturaQuiz
	 * @throws KalturaAPIException
	 */
	private function validateAndUpdateQuizData( entry $dbEntry, KalturaQuiz $quiz, $currentVersion = 0, kQuiz $newQuiz = null )
	{
		if ( !QuizPlugin::validateUserEntitledForQuizEdit($dbEntry) ) {
			KalturaLog::debug('Update quiz allowed only with admin KS or entry owner or co-editor');
			throw new KalturaAPIException(KalturaErrors::INVALID_USER_ID);
		}
		$quizData = $quiz->toObject($newQuiz);
		$quizData->setVersion( $currentVersion+1 );
		QuizPlugin::setQuizData( $dbEntry, $quizData );
		$dbEntry->setIsTrimDisabled( true );
		$dbEntry->save();
		$quiz->fromObject( $quizData );
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
	 * creates a pdf from quiz object
	 *
	 * @action serve
	 * @param string $entryId
	 * @param KalturaQuizFileType $quizFileType
	 * @return file
	 * @throws KalturaErrors::ENTRY_ID_NOT_FOUND
	 * @throws KalturaQuizErrors::PROVIDED_ENTRY_IS_NOT_A_QUIZ
	 * @throws KalturaQuizErrors::NO_SUCH_FILE_TYPE
	 */
	public function serveAction($entryId, $quizFileType)
	{
		KalturaLog::debug("Create a PDF Document for entry id [ " .$entryId. " ]");
		$dbEntry = entryPeer::retrieveByPK($entryId);

		if (!$dbEntry)
			throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $entryId);

		$kQuiz = QuizPlugin::getQuizData($dbEntry);
		if ( is_null( $kQuiz ) )
			throw new KalturaAPIException(KalturaQuizErrors::PROVIDED_ENTRY_IS_NOT_A_QUIZ, $entryId);

		if ($quizFileType == KalturaQuizFileType::PDF) {
			$kp = new kQuizPdf($entryId);
			$kp->createQuestionPdf();
			return $kp->submitDocument();
		}
		else
		{
			throw new KalturaAPIException(KalturaQuizErrors::NO_SUCH_FILE_TYPE);
		}
	}


	/**
	 * sends a with an api request for pdf from quiz object
	 *
	 * @action getUrl
	 * @param string $entryId
	 * @return string
	 * @throws KalturaErrors::ENTRY_ID_NOT_FOUND
	 * @throws KalturaQuizErrors::PROVIDED_ENTRY_IS_NOT_A_QUIZ
	 */
	public function getUrlAction($entryId)
	{
		KalturaLog::debug("Create a URL PDF Document download for entry id [ " .$entryId. " ]");

		$dbEntry = entryPeer::retrieveByPK($entryId);
		if (!$dbEntry)
			throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $entryId);

		$kQuiz = QuizPlugin::getQuizData($dbEntry);
		if ( is_null( $kQuiz ) )
			throw new KalturaAPIException(KalturaQuizErrors::PROVIDED_ENTRY_IS_NOT_A_QUIZ, $entryId);

		$finalPath ='/api_v3/index.php/service/quiz_quiz/action/serve/entryId/';
		$finalPath .="$entryId";

		$ksObj = $this->getKs();
		$ksStr = ($ksObj) ? $ksObj->getOriginalString() : null;
		$finalPath .= "/ks/".$ksStr;

		$partnerId = $this->getPartnerId();
		$downloadUrl = myPartnerUtils::getCdnHost($partnerId) . $finalPath;

		return $downloadUrl;
	}
}
