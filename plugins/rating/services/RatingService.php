<?php
/**
 * Allows user to manipulate their entry rating
 *
 * @service rating
 * @package plugins.rating
 * @subpackage api.services
 */

class RatingService extends KalturaBaseService
{
	public function initService($serviceId, $serviceName, $actionName)
	{
		parent::initService($serviceId, $serviceName, $actionName);
		
		if(!RatingPlugin::isAllowedPartner($this->getPartnerId()))
		{
			throw new KalturaAPIException(KalturaErrors::FEATURE_FORBIDDEN, RatingPlugin::PLUGIN_NAME);
		}
		
		if (kCurrentContext::$ks_object->isAnonymousSession() && $actionName !== 'getRatingCounts')
		{
			throw new KalturaAPIException(KalturaErrors::ANONYMOUS_ACCESS_FORBIDDEN);
		}
	}
	
	/**
	 * @action rate
	 * Action for current kuser ro rate a specific entry
	 * @param string $entryId
	 * @param int $rank
	 * @throws KalturaErrors::ENTRY_ID_NOT_FOUND
	 * @throws KalturaErrors::INVALID_RANK_VALUE
	 * @return int
	 */
	public function rateAction ($entryId, $rank)
	{
		$dbEntry = entryPeer::retrieveByPK($entryId);
		if (!$dbEntry)
		{
			throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $entryId);
		}
		
		//Check if a kvote for current entryId and kuser already exists.
		$existingKVote = kvotePeer::doSelectByEntryIdAndPuserId($entryId, $this->getPartnerId(), kCurrentContext::$ks_uid);
		if ($existingKVote)
		{
			$existingKVote->setRank($rank);
			$existingKVote->save();
		}
		else
		{
			kvotePeer::createKvote($entryId, $this->getPartnerId(), kCurrentContext::$ks_uid, $rank, KVoteType::RANK);
		}
		
		return $rank;
	}
	
	/**
	 * @action removeRating
	 * Action to remove current kuser's rating for a specific entry
	 * @param string $entryId
	 * @throws KalturaErrors::ENTRY_ID_NOT_FOUND
	 * @throws KalturaRatingErrors::USER_RATING_FOR_ENTRY_NOT_FOUND
	 * @return bool
	 */
	public function removeRatingAction ($entryId)
	{
		$dbEntry = entryPeer::retrieveByPK($entryId);
		if (!$dbEntry)
		{
			throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $entryId);
		}
		
		$existingKVote = kvotePeer::doSelectByEntryIdAndPuserId($entryId, $this->getPartnerId(), kCurrentContext::$ks_uid);
		if (!$existingKVote)
		{
			throw new KalturaAPIException(KalturaRatingErrors::USER_RATING_FOR_ENTRY_NOT_FOUND);
		}
		
		
		if (kvotePeer::disableExistingKVote($entryId, $this->getPartnerId(), kCurrentContext::$ks_uid))
		{
			return true;
		}
		
		return false;
	}
	
	/**
	 * @action checkRating
	 * Action to check current kuser's rating for a specific entry
	 * @param string $entryId
	 * @throws KalturaErrors::ENTRY_ID_NOT_FOUND
	 * @return int
	 */
	public function checkRatingAction ($entryId)
	{
		if (!$entryId)
		{
			throw new KalturaAPIException(KalturaErrors::MISSING_MANDATORY_PARAMETER, 'entryId');
		}
		
		$dbEntry = entryPeer::retrieveByPK($entryId);
		if (!$dbEntry)
		{
			throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $entryId);
		}
		
		$existingKVote = kvotePeer::doSelectByEntryIdAndPuserId($entryId, $this->getPartnerId(), kCurrentContext::$ks_uid);
		if (!$existingKVote)
		{
			throw new KalturaAPIException(KalturaRatingErrors::USER_RATING_FOR_ENTRY_NOT_FOUND);
		}
		
		return $existingKVote->getRank();
		
	}
	
	
	/**
	 * @action getRatingCounts
	 * Action to check entry's rating counts breakdown
	 * @param KalturaRatingCountFilter $filter
	 * @throws KalturaErrors::ENTRY_ID_NOT_FOUND
	 * @throws KalturaRatingErrors::USER_RATING_FOR_ENTRY_NOT_FOUND
	 * @return KalturaRatingCountListResponse
	 */
	public function getRatingCountsAction (KalturaRatingCountFilter $filter)
	{
		if(!$filter->entryIdEqual || !entryPeer::retrieveByPK($filter->entryIdEqual))
		{
			throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $filter->entryIdEqual);
		}
		
		if(!$filter->rankIn)
		{
			throw new KalturaAPIException(KalturaRatingErrors::USER_RATING_FOR_ENTRY_NOT_FOUND);
		}
		
		return $filter->getListResponse(new KalturaFilterPager(), null);
	}
}