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
	 * @return string
	 **/
	public function getVotesAction($pollId, $answerIds)
	{
		$votes = PollActions::getVotes($pollId, $answerIds);
		KalturaLog::info("Votes from Poll id : ".$pollId.", Are :".print_r($votes, true));
		return $votes;
	}

	/**
	 * Vote Action
	 * @action example
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