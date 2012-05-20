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
		2. if pk is not the umber - will try to retrieve
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
	
	public static function setDefaultCriteriaFilter()
	{
	    if(self::$s_criteria_filter == null)
			self::$s_criteria_filter = new criteriaFilter();
		
		$c = new myCriteria(); 
		$subCriterion1 = $c->getNewCriterion(PartnerPeer::PARTNER_PARENT_ID, kCurrentContext::$partner_id ? kCurrentContext::$partner_id : kCurrentContext::$ks_partner_id);
		$subCriterion2 = $c->getNewCriterion(PartnerPeer::ID, kCurrentContext::$partner_id ? kCurrentContext::$partner_id : kCurrentContext::$ks_partner_id);
		$subCriterion1->addOr($subCriterion2);
		$c->add($subCriterion1);
		
		self::$s_criteria_filter->setFilter($c);
	}
	
}
