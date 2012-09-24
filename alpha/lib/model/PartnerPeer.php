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
	
	public static function getPartnerPriorityFactor($partnerId, $urgency)
	{
		$partner = PartnerPeer::retrieveByPK($partnerId);
		$priority = self::getPriority($partner, $urgency);
		$priority2Factor = kConf::get('priority_factor');
		$priorityFactor = $priority2Factor[$priority];
		return $priorityFactor;
	}
	
	private static function getPriority($partner, $urgency)
	{
		// TODO : Bounded to kJobsManager::generateLockInfoData, when it changes - the next lines should be updated accordingly.
		//  In the future the urgency will be a factor of the urgency. for the time being, we will treat it only as isbulk factor
		$isBulk = BatchJobUrgencyType::isBulkUpload($urgency);
		$priorityGroup = PriorityGroupPeer::retrieveByPK($partner->getPriorityGroupId());
	
		if(!$priorityGroup)
		{
			if($isBulk)
				return PriorityGroup::DEFAULT_BULK_PRIORITY;
	
			return PriorityGroup::DEFAULT_PRIORITY;
		}
	
		if($isBulk)
			return $priorityGroup->getBulkPriority();
			
		return $priorityGroup->getPriority();
	}
}
