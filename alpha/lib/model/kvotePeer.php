<?php

/**
 * Subclass for performing query and update operations on the 'kvote' table.
 *
 * 
 *
 * @package Core
 * @subpackage model
 */ 
class kvotePeer extends BasekvotePeer
{
    public static function setDefaultCriteriaFilter()
    {
        if ( self::$s_criteria_filter == null )
		{
			self::$s_criteria_filter = new criteriaFilter ();
		}

		$c = new myCriteria();
		$c->add ( self::STATUS, KVoteStatus::KVOTE_STATUS_REVOKED, Criteria::NOT_EQUAL );
		
		self::$s_criteria_filter->setFilter ( $c );
    }
    
    
    public static function doSelectByEntryIdAndPuserId ($entryId, $partnerId, $puserId)
    {
        $kuser = self::getKuserFromPuserAndPartner($puserId, $partnerId);
        
        $c = new Criteria();
        $c->addAnd(kvotePeer::KUSER_ID, $kuser->getId(), Criteria::EQUAL);
        $c->addAnd(kvotePeer::ENTRY_ID, $entryId, Criteria::EQUAL);
        
        return self::doSelectOne($c);
    }
    
    public static function getKuserFromPuserAndPartner($puserId, $partnerId)
	{
		$kuser = kuserPeer::getKuserByPartnerAndUid($partnerId, $puserId, true);
		
		if ($kuser->getStatus() !== KuserStatus::ACTIVE)
			throw new APIException(APIErrors::INVALID_USER_ID);
		
		return $kuser;
	}
	
	public static function enableExistingKVote ($entryId, $partnerId, $puserId)
	{
	    self::setUseCriteriaFilter(false);
	    
	    $kvote = self::doSelectByEntryIdAndPuserId($entryId, $partnerId, $puserId);
	    if ($kvote && $kvote->getStatus() == KVoteStatus::KVOTE_STATUS_REVOKED)
	    {
	        self::changeKVoteStatus($kvote, KVoteStatus::KVOTE_STATUS_VOTED);
	        return true;
	    }
	    
	    return false;
	}
	
    public static function disableExistingKVote ($entryId, $partnerId, $puserId)
	{
	    $kvote = self::doSelectByEntryIdAndPuserId($entryId, $partnerId, $puserId);
	    if ($kvote->getStatus() == KVoteStatus::KVOTE_STATUS_VOTED)
	    {
	        self::changeKVoteStatus($kvote, KVoteStatus::KVOTE_STATUS_REVOKED);
	    } 
	}
	
	public static function changeKVoteStatus (kvote $kvote, $requiredStatus)
	{
	    $kvote->setStatus($requiredStatus);
	    $kvote->save();
	}
	
	public static function createKvote ($entryId, $partnerId, $puserId, $rank)
	{
	    $kvote = new kvote();
		$kvote->setEntryId($entryId);
		$kvote->setStatus(KVoteStatus::KVOTE_STATUS_VOTED);
		$kuser = self::getKuserFromPuserAndPartner($puserId, $partnerId);
		$kvote->setKuserId($kuser->getId());
		$kvote->setRank($rank);
		$kvote->save();
	}
}
