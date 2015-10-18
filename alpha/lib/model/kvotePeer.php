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
		$c->add ( kvotePeer::STATUS, KVoteStatus::REVOKED, Criteria::NOT_EQUAL );
		
		self::$s_criteria_filter->setFilter ( $c );
    }
    
    
    public static function doSelectByEntryIdAndPuserId ($entryId, $partnerId, $puserId)
    {
        $kuser = self::getKuserFromPuserAndPartner($puserId, $partnerId);
        if (!$kuser)
        {
            return null;
        }
        
        $c = new Criteria(); 
        $c->addAnd(kvotePeer::KUSER_ID, $kuser->getId(), Criteria::EQUAL);
        $c->addAnd(kvotePeer::ENTRY_ID, $entryId, Criteria::EQUAL);
        
        return self::doSelectOne($c);
    }
    
    protected static function getKuserFromPuserAndPartner($puserId, $partnerId, $shouldCreate = false)
	{
		$kuser = kuserPeer::getKuserByPartnerAndUid($partnerId, $puserId, true);
    		
		return $kuser;
	}
	
	public static function enableExistingKVote ($entryId, $partnerId, $puserId)
	{
	    self::setUseCriteriaFilter(false);
	    
	    $kvote = self::doSelectByEntryIdAndPuserId($entryId, $partnerId, $puserId);
	    if ($kvote)
	    {
	        $kvote->setStatus(KVoteStatus::VOTED);
	        $affectedLines = $kvote->save();
	    }
	    
	    return isset($affectedLines) ? $affectedLines : 0;
	}
	
    public static function disableExistingKVote ($entryId, $partnerId, $puserId)
	{
	    $kvote = self::doSelectByEntryIdAndPuserId($entryId, $partnerId, $puserId);
	    if ($kvote)
	    {
            $kvote->setStatus(KVoteStatus::REVOKED);
    	    $affectedLines = $kvote->save();
	    }
	    
	    return isset($affectedLines) ? $affectedLines : 0;
	    
	}
	
	public static function createKvote ($entryId, $partnerId, $puserId, $rank, $type=KVoteType::RANK)
	{
	    $kvote = new kvote();
		$kvote->setEntryId($entryId);
		$kvote->setStatus(KVoteStatus::VOTED);
		$kvote->setPartnerId($partnerId);
		$kvote->setKvoteType($type);
		$kuser = self::getKuserFromPuserAndPartner($puserId, $partnerId);
		if (!$kuser)
		{
		    $kuser = new kuser();
		    $kuser->setPuserId($puserId);
		    $kuser->setStatus(KuserStatus::ACTIVE);
		    $kuser->save();
		}
		$kvote->setPuserId($puserId);
		$kvote->setKuserId($kuser->getId());
		$kvote->setRank($rank);
		$kvote->save();
	}
}
