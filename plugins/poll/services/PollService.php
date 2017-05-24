<?php

/**
 * Poll service
 *
 * The poll service works against the cache entirely no DB instance should be used here
 *
 * @service poll
 * @package plugins.poll
 * @subpackage api.services
 * @throws KalturaErrors::SERVICE_FORBIDDEN
 */
class PollService extends KalturaBaseService
{

	/**
	 * Add Action
	 * @action add
	 * @param string $pollType
	 * @return string
	 * @throws KalturaAPIException
	 */
	public function addAction($pollType = 'SINGLE_ANONYMOUS')
	{
		try {
			return PollActions::generatePollId($pollType);
		}
		catch (Exception $e)
		{
			throw new KalturaAPIException($e->getMessage());
		}

	}

	/**
	 * Get Votes Action
	 * @action getVotes
	 * @param string $pollId
	 * @param string $answerIds
	 * @param string $otherDCVotes
	 * @return string
	 * @throws KalturaAPIException
	 */
	public function getVotesAction($pollId, $answerIds, $otherDCVotes = null)
	{
		try {
			$localDcVotes = PollActions::getVotes($pollId, $answerIds);
		}
		catch (Exception $e)
		{
			throw new KalturaAPIException($e->getMessage());
		}

		if (!$otherDCVotes)
		{
			$_POST['otherDCVotes'] = json_encode($localDcVotes);
			$remoteDCIds = kDataCenterMgr::getAllDcs();
			if($remoteDCIds && count($remoteDCIds) > 0)
			{
				$remoteDCHost = kDataCenterMgr::getRemoteDcExternalUrlByDcId(1 - kDataCenterMgr::getCurrentDcId());
				if ($remoteDCHost)
					return kFileUtils::dumpApiRequest($remoteDCHost);
			}
		}
		else
		{
			$prevData = json_decode($otherDCVotes);
			$localDcVotes->merge($prevData);
		}
		return json_encode($localDcVotes);
	}

	/**
	 * Vote Action
	 * @action vote
	 * @param string $pollId
	 * @param string $userId
	 * @param string $answerIds
	 * @return string
	 * @throws KalturaAPIException
	 */
	public function voteAction($pollId, $userId, $answerIds)
	{
		try {
			return PollActions::setVote($pollId, $userId, $answerIds);
		}
		catch (Exception $e)
		{
			throw new KalturaAPIException($e->getMessage());
		}
	}

	/**
	 * Should return true or false for allowing/disallowing kaltura network filter for the given action.
	 * Can be extended to partner specific checks etc...
	 * @return true if "kaltura network" is enabled for the given action or false otherwise
	 * @param string $actionName action name
	 */
	protected function kalturaNetworkAllowed($actionName)
	{
		return false;
	}

}
