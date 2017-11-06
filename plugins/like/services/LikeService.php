<?php
/**
 * Allows user to 'like' or 'unlike' and entry
 *
 * @service like
 * @package plugins.like
 * @subpackage api.services
 */
class LikeService extends KalturaBaseService
{
    const KVOTE_LIKE_RANK_VALUE = 1;
    const KVOTE_UNLIKE_RANK_VALUE = 0;
    
    public function initService($serviceId, $serviceName, $actionName)
    {
        parent::initService($serviceId, $serviceName, $actionName);
		
		if(!LikePlugin::isAllowedPartner($this->getPartnerId()))
		{
		    throw new KalturaAPIException(KalturaErrors::FEATURE_FORBIDDEN, LikePlugin::PLUGIN_NAME);
		}	
		
		if ((!kCurrentContext::$ks_uid || kCurrentContext::$ks_uid == "") && $actionName != "list")
		{
		    throw new KalturaAPIException(KalturaErrors::INVALID_USER_ID);
		}
    }
    
    /**
     * @action like
     * Action for current kuser to mark the entry as "liked".
     * @param string $entryId
     * @throws KalturaLikeErrors::USER_LIKE_FOR_ENTRY_ALREADY_EXISTS
     * @throws KalturaErrors::ENTRY_ID_NOT_FOUND
     * @return bool
     */
    public function likeAction ( $entryId )
    {
        if (!$entryId)
	    {
	        throw new KalturaAPIException(KalturaErrors::MISSING_MANDATORY_PARAMETER, "entryId");
	    }
	    
	    //Check if a kvote for current entryId and kuser already exists. If it does - throw exception
	    $existingKVote = kvotePeer::doSelectByEntryIdAndPuserId($entryId, $this->getPartnerId(), kCurrentContext::$ks_uid);
	    if ($existingKVote)
	    {
	        throw new KalturaAPIException(KalturaLikeErrors::USER_LIKE_FOR_ENTRY_ALREADY_EXISTS);
	    }
	    
	    $dbEntry = entryPeer::retrieveByPK($entryId);
		if (!$dbEntry)
			throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $entryId);
			
		if (kvotePeer::enableExistingKVote($entryId, $this->getPartnerId(), kCurrentContext::$ks_uid))
		{
		    return true;
		}
		
		kvotePeer::createKvote($entryId, $this->getPartnerId(), kCurrentContext::$ks_uid, self::KVOTE_LIKE_RANK_VALUE, KVoteType::LIKE);
	    
	    return true;
    }
    
    /**
     * @action unlike
     * Action for current kuser to revoke a previously added "like" from an entry
     * @param string $entryId
     * @return bool
     */
    public function unlikeAction ( $entryId )
    {
        if (!$entryId)
	    {
	        throw new KalturaAPIException(KalturaErrors::MISSING_MANDATORY_PARAMETER, "entryId");
	    }
	    
	    $existingKVote = kvotePeer::doSelectByEntryIdAndPuserId($entryId, $this->getPartnerId(), kCurrentContext::$ks_uid);
	    if (!$existingKVote)
	    {
	        throw new KalturaAPIException(KalturaLikeErrors::USER_LIKE_FOR_ENTRY_NOT_FOUND);
	    }
	    
	    $dbEntry = entryPeer::retrieveByPK($entryId);
		if (!$dbEntry)
			throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $entryId);
        
		if (kvotePeer::disableExistingKVote($entryId, $this->getPartnerId(), kCurrentContext::$ks_uid))
		    return true;
		
		return false;
    
    }
    
    /**
     * @action checkLikeExists
     * Action to check whether a user likes a specific entry
     * @param string $entryId
     * @param string $userId
     * @return bool
     */
    public function checkLikeExistsAction ( $entryId , $userId = null )
    {
        if (!$entryId)
	    {
	        throw new KalturaAPIException(KalturaErrors::MISSING_MANDATORY_PARAMETER, "entryId");
	    }
        
	    if (!$userId)
	    {
	        $userId = kCurrentContext::$ks_uid;
	    }
	    
	    $existingKVote = kvotePeer::doSelectByEntryIdAndPuserId($entryId, $this->getPartnerId(), $userId);
	    if (!$existingKVote || !count($existingKVote))
	    {
	        return false;
	    }
	    
	    return true;
        	    
    }

	/**
	 * @action list
	 * @param KalturaLikeFilter $filter
	 * @param KalturaFilterPager $pager
	 * @return KalturaLikeListResponse
	 */
	public function listAction(KalturaLikeFilter $filter = null, KalturaFilterPager $pager = null)
	{
		if(!$filter)
			$filter = new KalturaLikeFilter();
		else	
		{			
			if($filter->entryIdEqual && !entryPeer::retrieveByPK($filter->entryIdEqual))			
				throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $filter->entryIdEqual);			
			if($filter->userIdEqual)
			{
				$kuser = kuserPeer::getActiveKuserByPartnerAndUid(kCurrentContext::$ks_partner_id, $filter->userIdEqual);
				if(!$kuser)
					throw new KalturaAPIException(KalturaErrors::USER_NOT_FOUND);
				
				$filter->userIdEqual = $kuser->getId();
			}
		}
		
		if(!$pager)
			$pager = new KalturaFilterPager();

		return $filter->getListResponse($pager, null);
	}
	
}
