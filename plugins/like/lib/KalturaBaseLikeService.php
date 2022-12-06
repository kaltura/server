<?php
/**
 * @package plugins.like
 * @subpackage api.services
 */
class KalturaBaseLikeService extends KalturaBaseService
{
	const KVOTE_LIKE_RANK_VALUE = 1;
	
	function likeImpl($entryId)
	{
		//Check if a kvote for current entryId and kuser already exists. If it does - throw exception
		$existingKVote = kvotePeer::doSelectByEntryIdAndPuserId($entryId, $this->getPartnerId(), kCurrentContext::$ks_uid);
		if ($existingKVote)
		{
			throw new KalturaAPIException(KalturaLikeErrors::USER_LIKE_FOR_ENTRY_ALREADY_EXISTS);
		}
		
		$dbEntry = entryPeer::retrieveByPK($entryId);
		if (!$dbEntry)
		{
			throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $entryId);
		}
		
		if (kvotePeer::enableExistingKVote($entryId, $this->getPartnerId(), kCurrentContext::$ks_uid))
		{
			return true;
		}
		
		kvotePeer::createKvote($entryId, $this->getPartnerId(), kCurrentContext::$ks_uid, self::KVOTE_LIKE_RANK_VALUE, KVoteType::LIKE);
		return true;
	}
	
	function unLikeImpl($entryId)
	{
		$existingKVote = kvotePeer::doSelectByEntryIdAndPuserId($entryId, $this->getPartnerId(), kCurrentContext::$ks_uid);
		if (!$existingKVote)
		{
			throw new KalturaAPIException(KalturaLikeErrors::USER_LIKE_FOR_ENTRY_NOT_FOUND);
		}
		
		$dbEntry = entryPeer::retrieveByPK($entryId);
		if (!$dbEntry)
		{
			throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $entryId);
		}
		
		if (kvotePeer::disableExistingKVote($entryId, $this->getPartnerId(), kCurrentContext::$ks_uid))
		{
			return true;
		}
		
		return false;
	}
}