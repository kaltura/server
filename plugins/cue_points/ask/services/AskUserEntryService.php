<?php

/**
 *
 * @service askUserEntry
 * @package plugins.ask
 * @subpackage api.services
 */
class AskUserEntryService extends KalturaBaseService{

	/**
	 * Submits the ask so that it's status will be submitted and calculates the score for the ask
	 *
	 * @action submitAsk
	 * @actionAlias userEntry.submitAsk
	 * @param int $id
	 * @return KalturaAskUserEntry
	 * @throws KalturaAPIException
	 */
	public function submitAskAction($id)
	{
		$dbUserEntry = UserEntryPeer::retrieveByPK($id);
		if (!$dbUserEntry)
			throw new KalturaAPIException(KalturaErrors::INVALID_OBJECT_ID, $id);
		
		if ($dbUserEntry->getType() != AskPlugin::getCoreValue('UserEntryType',AskUserEntryType::ASK))
			throw new KalturaAPIException(KalturaAskErrors::PROVIDED_ENTRY_IS_NOT_A_ASK, $id);
		
		$dbUserEntry->setStatus(AskPlugin::getCoreValue('UserEntryStatus', AskUserEntryStatus::ASK_SUBMITTED));
		$userEntry = new KalturaAskUserEntry();
		$userEntry->fromObject($dbUserEntry, $this->getResponseProfile());
		$entryId = $dbUserEntry->getEntryId();
		$entry = entryPeer::retrieveByPK($entryId);
		if(!$entry)
			throw new KalturaAPIException(KalturaErrors::INVALID_OBJECT_ID, $entryId);
		
		$kAsk = AskPlugin::getAskData($entry);
		if (!$kAsk)
			throw new KalturaAPIException(KalturaAskErrors::PROVIDED_ENTRY_IS_NOT_A_ASK, $entryId);
		
		list($score, $numOfCorrectAnswers) = $dbUserEntry->calculateScoreAndCorrectAnswers();
		$dbUserEntry->setScore($score);
		$dbUserEntry->setNumOfCorrectAnswers($numOfCorrectAnswers);	
		if ($kAsk->getShowGradeAfterSubmission()== KalturaNullableBoolean::TRUE_VALUE || $this->getKs()->isAdmin() == true)
		{
			$userEntry->score = $score;
		}
		else
		{
			$userEntry->score = null;
		}

		$c = new Criteria();
		$c->add(CuePointPeer::ENTRY_ID, $dbUserEntry->getEntryId(), Criteria::EQUAL);
		$c->add(CuePointPeer::TYPE, AskPlugin::getCoreValue('CuePointType', AskCuePointType::ASK_QUESTION));
		$dbUserEntry->setNumOfQuestions(CuePointPeer::doCount($c));
		$dbUserEntry->setStatus(AskPlugin::getCoreValue('UserEntryStatus', AskUserEntryStatus::ASK_SUBMITTED));
		$dbUserEntry->save();

		return $userEntry;
	}
}
