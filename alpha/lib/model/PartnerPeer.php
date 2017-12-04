<?php

/**
 * Subclass for performing query and update operations on the 'partner' table.
 * Because Partner is very often used in the SDK and the object itself hardley changes,
 * we'll use the obejct cache mechanism to hit the DB as little as possible
 *
 *
 * @package Core
 * @subpackage model
 */
class PartnerPeer extends BasePartnerPeer
{
	const NULL_PARTNER = "_NULL_" ;
	const CLZZ = "Partner" ;
	
	const GLOBAL_PARTNER = 0;
	
	const KALTURAS_PARTNER_EMAIL_CHANGE = 52;
	/*
		Will retrieve the partner object in one of 2 ways:
		1. if pk in a number - will use the original  retrieveByPK
		2. if pk is not the number - will try to retrieve
	*/

	// partner cache functions, left for backward compatibility
	public static function resetPartnerInCache ( $key , $by_id = true )
	{
	}

	public static function removePartnerFromCache ( $id )
	{
	}

	public static function getCacheInvalidationKeys()
	{
		return array(array("partner:id=%s", self::ID));		
	}
	
	/**
	 * Function returns the default criteria of the partner class
	 * @return Criteria
	 */
	public static function getDefaultCriteria ()
	{
	    $partnerId = kCurrentContext::$partner_id ? kCurrentContext::$partner_id : kCurrentContext::$ks_partner_id;
	    
	    $c = new Criteria();
	    $subCriterion1 = $c->getNewCriterion(PartnerPeer::PARTNER_PARENT_ID, $partnerId);
		$subCriterion2 = $c->getNewCriterion(PartnerPeer::ID, $partnerId);
		$subCriterion1->addOr($subCriterion2);
		$c->add($subCriterion1);
		
		return $c;
	}
	
	public static function getPartnerPriorityFactor($partnerId)
	{
		$partner = PartnerPeer::retrieveByPK($partnerId);
		return self::getPartnerPriorityFactorByPartner($partner);
	}
	
	public static function getPartnerPriorityFactorByPartner($partner)
	{
		$priority = self::getPriority($partner);
		$priority2Factor = kConf::get('priority_factor');
		$priorityFactor = $priority2Factor[$priority];
		return $priorityFactor;
	}
	
	private static function getPriority(Partner $partner)
	{
		$priorityGroup = $partner->getPriorityGroupId() ? PriorityGroupPeer::retrieveByPK($partner->getPriorityGroupId()) : null;
	
		if(!$priorityGroup)
			return PriorityGroup::DEFAULT_PRIORITY;
	
		return $priorityGroup->getPriority();
	}
	
	public static function retrieveActiveByPK($pk)
	{
		$partner = PartnerPeer::retrieveByPK($pk);
		
		if(!$partner || $partner->getStatus() !== Partner::PARTNER_STATUS_ACTIVE)
			return null;
		
		return $partner;
	}
}
