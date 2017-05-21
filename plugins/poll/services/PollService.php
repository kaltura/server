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

	public function initService($serviceId, $serviceName, $actionName)
	{
		parent::initService($serviceId, $serviceName, $actionName);

		if(!PollPlugin::isAllowedPartner($this->getPartnerId()))
			throw new KalturaAPIException(KalturaErrors::FEATURE_FORBIDDEN, PollPlugin::PLUGIN_NAME);

		$this->applyPartnerFilterForClass('poll');

	}

	/**
	 * Add Action
	 * @action add
	 * @param string $pollType
	 * @return string
	 **/
	public function addAction($pollType = 'SINGLE_ANONYMOUS')
	{
		return PollActions::generatePollId($pollType);
	}

	/**
	 * Get Votes Action
	 * @action getVotes
	 * @param string $pollId
	 * @param string $answerIds comma separated string
	 * @param string $previousDataJson
	 * @return string
	 **/
	public function getVotesAction($pollId, $answerIds, $previousDataJson = null)
	{
		$localDcVotes = PollActions::getVotes($pollId, $answerIds);
		if (!$previousDataJson)
		{
			$_POST['previousDataJson'] = json_encode($localDcVotes);
			$remoteDcIds = kDataCenterMgr::getAllDcs();
			if($remoteDcIds && count($remoteDcIds) > 0) {
				$remoteDCHost = kDataCenterMgr::getRemoteDcExternalUrlByDcId(1 - kDataCenterMgr::getCurrentDcId());
				if ($remoteDCHost)
					return kFileUtils::dumpApiRequest($remoteDCHost);
			}
		}
		else
		{
			$prevData = json_decode($previousDataJson);
			$localDcVotes->merge($prevData);
		}
		return json_encode($localDcVotes);
	}

	/**
	 * Vote Action
	 * @action vote
	 * @param string $pollId
	 * @param string $userId
	 * @param string $answerIds comma separated string
	 * @return string
	 **/
	public function voteAction($pollId, $userId, $answerIds)
	{
		return PollActions::setVote($pollId, $userId, $answerIds);
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