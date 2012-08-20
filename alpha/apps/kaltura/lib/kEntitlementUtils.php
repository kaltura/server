<?php
/**
 * kEntitlementUtils is all utils needed for entitlement use cases.
 * @package Core
 * @subpackage utils
 *
 */
class kEntitlementUtils 
{
	const DEFAULT_CONTEXT = 'DEFAULTPC';
	const NOT_DEFAULT_CONTEXT = 'NOTDEFAULTPC';
	
	const ENTRY_PRIVACY_CONTEXT = 'ENTRYPC'; 
	
	protected static $initialized = false;
	protected static $entitlementEnforcement = false;
	protected static $privacyContextSearch = null;	
	
	public static function getEntitlementEnforcement()
	{
		return self::$entitlementEnforcement;
	}
	
	public static function getInitialized()
	{
		return self::$initialized;
	}
	
	public static function isKsPrivacyContextSet()
	{
		$ks = ks::fromSecureString(kCurrentContext::$ks);
		
		if(!$ks || !$ks->getPrivacyContext())
			return false;
			
		return true;		
	}
	
	/**
	 * Returns true if kuser or current kuser is entitled to entryId
	 * @param entry $entry
	 * @param int $kuser
	 * @return bool
	 */
	public static function isEntryEntitled(entry $entry, $kuserId = null)
	{
		$ks = ks::fromSecureString(kCurrentContext::$ks);
		
		// entry is entitled when entitlement is disable
		// for actions with no ks - need to check if partner have default entitlement feature enable.
		if(!self::getEntitlementEnforcement() && $ks)
		{
			KalturaLog::debug('Entry entitled: entitlement disabled');
			return true;
		}
		
		$partner = $entry->getPartner();
		
		if(!$ks && !$partner->getDefaultEntitlementEnforcement())
		{
			KalturaLog::debug('Entry [' . print_r($entry->getId(), true) . '] entitled: no ks and default is with no enforcement');
			return true;
		}
		
		if($ks && $ks->isWidgetSession() && $ks->getDisableEntitlementForEntry() == $entry->getId())
		{
			KalturaLog::debug('Entry [' . print_r($entry->getId(), true) . '] entitled: widget session that disble entitlement for this entry');
			return true;
		}
		
		$allCategoriesEntry = categoryEntryPeer::retrieveActiveAndPendingByEntryId($entry->getId());
		
		$categories = array();
		foreach($allCategoriesEntry as $categoryEntry)	
			$categories[] = $categoryEntry->getCategoryId();
			
		//if entry doesn't belong to any category. 
		$categories[] = category::CATEGORY_ID_THAT_DOES_NOT_EXIST;
			
		$c = KalturaCriteria::create(categoryPeer::OM_CLASS); 
		$c->add(categoryPeer::ID, $categories, Criteria::IN);
		
				
		$privacy = array(PrivacyType::ALL);
		if($ks && !$ks->isWidgetSession())
			$privacy[] = PrivacyType::AUTHENTICATED_USERS;
			
		$crit = $c->getNewCriterion (categoryPeer::PRIVACY, $privacy, Criteria::IN);
		
		$ksPrivacyContexts = null;
		
		// entry that doesn't belong to any category is public
		//when ks is not provided - the entry is still public (for example - download action)
		$categoryEntries = categoryEntryPeer::retrieveActiveByEntryId($entry->getId());
		if(!count($categoryEntries) && !$ks)
		{
			KalturaLog::debug('Entry [' . print_r($entry->getId(), true) . '] entitled: entry does not belong to any category');
			return true;
		}
		
		if($ks)
		{	
			$ksPrivacyContexts = $ks->getPrivacyContext();
			if (!$ksPrivacyContexts || trim($ksPrivacyContexts) == '')
			{
				$ksPrivacyContexts = self::DEFAULT_CONTEXT . $partner->getId();
				
				if(!count($allCategoriesEntry))
				{
					// entry that doesn't belong to any category is public
					KalturaLog::debug('Entry [' . print_r($entry->getId(), true) . '] entitled: entry does not belong to any category and privacy context on the ks is not set');
					return true;
				}
			}
			
			$c->add(categoryPeer::PRIVACY_CONTEXTS, $ksPrivacyContexts, KalturaCriteria::IN_LIKE);
			
			if(!$kuserId)
			{
				$partnerId = kCurrentContext::$partner_id ? kCurrentContext::$partner_id : kCurrentContext::$ks_partner_id;
				$kuser = kuserPeer::getKuserByPartnerAndUid($partnerId, kCurrentContext::$ks_uid, true);
				if($kuser)
					$kuserId = $kuser->getId();
			}
			
			if($kuserId)
			{
				// kuser is set on the entry as creator or uploader
				if ($kuserId != '' && ($entry->getKuserId() == $kuserId))
				{
					KalturaLog::debug('Entry [' . print_r($entry->getId(), true) . '] entitled: ks user is the same as entry->kuserId or entry->creatorKuserId [' . $kuserId . ']');
					return true;
				}
				
				// kuser is set on the entry entitled users edit or publish
				$entitledKusers = array_merge(explode(',', $entry->getEntitledKusersEdit()), explode(',', $entry->getEntitledKusersPublish()));
				if(in_array($kuserId, $entitledKusers))
				{
					KalturaLog::debug('Entry [' . print_r($entry->getId(), true) . '] entitled: ks user is the same as entry->entitledKusersEdit or entry->entitledKusersPublish');
					return true;
				} 
			}
			
			// kuser is set on the category as member
			// this ugly code is temporery - since we have a bug in sphinxCriteria::getAllCriterionFields
			if($kuserId)
			{
				$membersCrit = $c->getNewCriterion ( categoryPeer::MEMBERS , $kuserId, Criteria::LIKE);
				$membersCrit->addOr($crit);
				$crit = $membersCrit;	
			}
			
		}
		else 
		{
			//no ks = set privacy context to default.
			$c->add(categoryPeer::PRIVACY_CONTEXTS, array(self::DEFAULT_CONTEXT . $partner->getId()), KalturaCriteria::IN_LIKE);
		}
		
		$c->addAnd($crit);
		
		//remove default FORCED criteria since categories that has display in search = public - doesn't mean that all of their entries are public
		KalturaCriterion::disableTag(KalturaCriterion::TAG_ENTITLEMENT_CATEGORY);
		$category = categoryPeer::doSelectOne($c);
		KalturaCriterion::restoreTag(KalturaCriterion::TAG_ENTITLEMENT_CATEGORY);

		if($category)
		{
			KalturaLog::debug('Entry [' . print_r($entry->getId(), true) . '] entitled: ks user is a member of this category or category privacy is set to public of authenticated');
			return true;
		}
		
		KalturaLog::debug('Entry [' . print_r($entry->getId(), true) . '] not entitled');
		return false;
	} 	

	/**
	 * Returns true if kuser or current kuser is entitled to assign entry to categoryId
	 * @param int $categoryId
	 * @param int $kuser
	 * @return bool
	 */
	public static function validateEntryAssignToCategory($categoryId, $kuserId = null)
	{ 
		if(!self::getEntitlementEnforcement())
			return true;
			
		$category = categoryPeer::retrieveByPK($categoryUser->categoryId);
		if (!$category)
			throw new KalturaAPIException(KalturaErrors::CATEGORY_NOT_FOUND, $categoryUser->categoryId);

		if ($category->getContributionPolicy() == ContributionPolicyType::ALL)
			return true;
		
		if($kuserId)
		{
			$partnerId = kCurrentContext::$partner_id ? kCurrentContext::$partner_id : kCurrentContext::$ks_partner_id;
			$kuser = kuserPeer::getKuserByPartnerAndUid($partnerId, kCurrentContext::$ks_uid);
			$kuserId = $kuser->getId();
		}
			
		$currentKuserCategoryKuser = categoryKuserPeer::retrieveByCategoryIdAndActiveKuserId($categoryUser->categoryId, $kuserId);
		if($currentKuserCategoryKuser && ($currentKuserCategoryKuser->getPermissionLevel() == CategoryKuserPermissionLevel::MANAGER ||
										  $currentKuserCategoryKuser->getPermissionLevel() == CategoryKuserPermissionLevel::MODERATOR ||
										  $currentKuserCategoryKuser->getPermissionLevel() == CategoryKuserPermissionLevel::CONTRIBUTOR))
			return true;
			
		return false;
	}
	
	/**
	 * Set Entitlement Enforcement - if entitelement is enabled \ disabled in this session
	 * @param int $categoryId
	 * @param int $kuser
	 * @return bool
	 */
	public static function initEntitlementEnforcement($partnerId = null, $enableEntit = null)
	{
		self::$initialized = true; 
		
		if(is_null($partnerId)) 
			$partnerId = kCurrentContext::$partner_id ? kCurrentContext::$partner_id : kCurrentContext::$ks_partner_id;
			
		if(is_null($partnerId))
			return;
			 
		$partner = PartnerPeer::retrieveByPK($partnerId);
		if (!$partner)
			return;
			
		if(!PermissionPeer::isValidForPartner(PermissionName::FEATURE_ENTITLEMENT, $partnerId))
			return;		
		
		$partnerDefaultEntitlementEnforcement = $partner->getDefaultEntitlementEnforcement();
		
		// default entitlement scope is true - enable.
		if(is_null($partnerDefaultEntitlementEnforcement))
			$partnerDefaultEntitlementEnforcement = true;
		
		self::$entitlementEnforcement = $partnerDefaultEntitlementEnforcement;
		
		$ksString = kCurrentContext::$ks ? kCurrentContext::$ks : '';
		if ($ksString != '') // for actions with no KS or when creating ks.
		{
			$ks = ks::fromSecureString($ksString);
			$enableEntitlement = $ks->getDisableEntitlement();
			if ($enableEntitlement)
				self::$entitlementEnforcement = false;
				
			$enableEntitlement = $ks->getEnableEntitlement();
			if ($enableEntitlement)
				self::$entitlementEnforcement = true;
		}
		
		if(!is_null($enableEntit))
		{
			if($enableEntit)
				self::$entitlementEnforcement = true;
			else
				self::$entitlementEnforcement = false;
		}
			
		if (self::$entitlementEnforcement)
		{
			KalturaCriterion::enableTag(KalturaCriterion::TAG_ENTITLEMENT_ENTRY);
			KalturaCriterion::enableTag(KalturaCriterion::TAG_ENTITLEMENT_CATEGORY);
		}
	}
	
	public static function getPrivacyForKs()
	{
		$ks = ks::fromSecureString(kCurrentContext::$ks);
		if(!$ks || $ks->isWidgetSession())
			return array(PrivacyType::ALL);
			
		return array(PrivacyType::ALL, PrivacyType::AUTHENTICATED_USERS);
	}
	
	public static function getPrivacyContextSearch()
	{
		$partnerId = kCurrentContext::$partner_id ? kCurrentContext::$partner_id : kCurrentContext::$ks_partner_id;
		
		if (self::$privacyContextSearch)
			return self::$privacyContextSearch;
			 
		$privacyContextSearch = array();
			
		$ks = ks::fromSecureString(kCurrentContext::$ks);
		if(!$ks)
			return array(self::DEFAULT_CONTEXT . $partnerId . '_' . PrivacyType::ALL);
			
		$ksPrivacyContexts = $ks->getPrivacyContext();
		
		if(is_null($ksPrivacyContexts))
			$ksPrivacyContexts = self::DEFAULT_CONTEXT . $partnerId;
		
		$ksPrivacyContexts = explode(',', $ksPrivacyContexts);
		
		foreach ($ksPrivacyContexts as $ksPrivacyContext)
		{
			$privacyContextSearch[] = $ksPrivacyContext . '_' . PrivacyType::ALL;
			
			if (!$ks->isWidgetSession())
				$privacyContextSearch[] = $ksPrivacyContext . '_' . PrivacyType::AUTHENTICATED_USERS;
		}
		
		self::$privacyContextSearch = $privacyContextSearch;
			 
		return $privacyContextSearch;
	}
	
	public static function setPrivacyContextSearch($privacyContextSearch)
	{
		self::$privacyContextSearch = array($privacyContextSearch . '_' . PrivacyType::ALL);
	}
	
	public static function getPrivacyContextForEntry(entry $entry)
	{		
		$privacyContexts = array();
		$entryPrivacy = null;
		$categories = array();		
		
		if (count($entry->getAllCategoriesIds(true)))
		{
			$c = KalturaCriteria::create(categoryPeer::OM_CLASS);
			KalturaCriterion::disableTag(KalturaCriterion::TAG_ENTITLEMENT_CATEGORY); 
			$c->add(categoryPeer::ID, $entry->getAllCategoriesIds(true), Criteria::IN);
			KalturaCriterion::restoreTag(KalturaCriterion::TAG_ENTITLEMENT_CATEGORY);
			
			KalturaCriterion::disableTag(KalturaCriterion::TAG_ENTITLEMENT_CATEGORY);
			$categories = categoryPeer::doSelect($c);
			KalturaCriterion::restoreTag(KalturaCriterion::TAG_ENTITLEMENT_CATEGORY);
			
			foreach ($categories as $category)
			{								
				$categoryPrivacy = $category->getPrivacy();
				$categoryPrivacyContexts = $category->getPrivacyContexts();
				if(!$categoryPrivacyContexts)
					$categoryPrivacyContexts = self::DEFAULT_CONTEXT . $entry->getPartnerId();
				
				$categoryPrivacyContexts = explode(',', $categoryPrivacyContexts);
				
				foreach ($categoryPrivacyContexts as $categoryPrivacyContext)
				{
					if(trim($categoryPrivacyContext) == '')
						 $categoryPrivacyContext = self::DEFAULT_CONTEXT . $entry->getPartnerId();
						 
					if(!isset($privacyContexts[$categoryPrivacyContext]) || $privacyContexts[$categoryPrivacyContext] > $categoryPrivacy)
						$privacyContexts[trim($categoryPrivacyContext)] = $categoryPrivacy;
				}
			}
		}
		
		//Entry That doesn't assinged to any category is public.
		if (!count($categories))
			$privacyContexts[self::DEFAULT_CONTEXT . $entry->getPartnerId()] = PrivacyType::ALL ;
		
		$entryPrivacyContexts = array();
		foreach ($privacyContexts as $categoryPrivacyContext => $Privacy)
			$entryPrivacyContexts[] = $categoryPrivacyContext . '_' . $Privacy;
		
		KalturaLog::debug('Privacy by context: ' . print_r($entryPrivacyContexts,true));
			
		return $entryPrivacyContexts;
	}
	
	public static function getEntitledKuserByPrivacyContext()
	{
		$partnerId = kCurrentContext::$partner_id ? kCurrentContext::$partner_id : kCurrentContext::$ks_partner_id;
		
		if(kCurrentContext::$ks_kuser_id && kCurrentContext::$ks_kuser_id == '')
			return null;
			
		$privacyContextSearch = array();
			
		$ks = ks::fromSecureString(kCurrentContext::$ks);
		$ksPrivacyContexts = $ks->getPrivacyContext();
		
		if(is_null($ksPrivacyContexts) || $ksPrivacyContexts == '')
			$ksPrivacyContexts = self::DEFAULT_CONTEXT . $partnerId;
		
		$ksPrivacyContexts = explode(',', $ksPrivacyContexts);
		
		foreach ($ksPrivacyContexts as $ksPrivacyContext)
			$privacyContextSearch[] = $ksPrivacyContext . '_' . kCurrentContext::$ks_kuser_id;
		
		$privacyContextSearch[] = self::ENTRY_PRIVACY_CONTEXT . '_' . kCurrentContext::$ks_kuser_id;
			
		return $privacyContextSearch;
	}
	
	public static function getKsPrivacyContext()
	{
		$partnerId = kCurrentContext::$ks_partner_id ? kCurrentContext::$ks_partner_id : kCurrentContext::$partner_id;
		
		$ks = ks::fromSecureString(kCurrentContext::$ks);
		if(!$ks)
			return array(self::DEFAULT_CONTEXT . $partnerId);
			
		$ksPrivacyContexts = $ks->getPrivacyContext();
		if(is_null($ksPrivacyContexts) || $ksPrivacyContexts == '')
			return array(self::DEFAULT_CONTEXT . $partnerId);
			
		return explode(',', $ksPrivacyContexts);
	}
}