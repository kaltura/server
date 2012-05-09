<?php
/**
 * kEntitlementUtils is all utils needed for entitlement use cases.
 * @package Core
 * @subpackage utils
 *
 */
class kEntitlementUtils 
{
	const DEFAULT_CONTEXT = 'DEFAULT_CONTEXT';
	const PRIVACY_CONTEXT_PREFIX = 'pc_pre_';
	
	protected static $entitlementEnforcement = false;  
	
	
	public static function getEntitlementEnforcement()
	{
		return self::$entitlementEnforcement;
	}
	
	/**
	 * Returns true if kuser or current kuser is entitled to entryId
	 * @param entry $entry
	 * @param int $kuser
	 * @return bool
	 */
	public static function isEntryEntitled(entry $entry, $kuserId = null)
	{
		// entry is entitled when entitlement is disable
		if(!self::getEntitlementEnforcement())
			return true;
		
		$ks = ks::fromSecureString(kCurrentContext::$ks);
		if($ks && $ks->isWidgetSession() && $ks->getDisableEntitlementForEntry() == $entry->getId())
			return true;
			
		$c = KalturaCriteria::create(categoryPeer::OM_CLASS); 
		$c->add(categoryPeer::ID, explode(',', $entry->getCategoriesIds()), Criteria::IN);
		
		$ksPrivacyContexts = null;
		$ks = ks::fromSecureString(kCurrentContext::$ks);
		
		$privacy = array(PrivacyType::ALL);
		if($ks && !$ks->isWidgetSession())
			$privacy[] = PrivacyType::AUTHENTICATED_USERS;
			
		$crit = $c->getNewCriterion (categoryPeer::PRIVACY, $privacy, Criteria::IN);
		
		if($ks)
		{	
			$ksPrivacyContexts = $ks->getPrivacyContext();
			if (!$ksPrivacyContexts)
				$ksPrivacyContexts = self::DEFAULT_CONTEXT;
			
			$c->add(categoryPeer::PRIVACY_CONTEXTS, $ksPrivacyContexts, Criteria::EQUAL);
			
			if(!$kuserId)
			{
				$partnerId = kCurrentContext::$partner_id ? kCurrentContext::$partner_id : kCurrentContext::$ks_partner_id;
				$kuser = kuserPeer::getKuserByPartnerAndUid($partnerId, kCurrentContext::$ks_uid);
				if($kuser)
					$kuserId = $kuser->getId();
			}
			
			// kuser is set on the entry as creator or uploader
			if ($entry->getKuserId() == $kuserId || $entry->getCreatorKuserId() == $kuserId)
				return true;
			
			// kuser is set on the entry entitled users edit or publish
			$entitledKusers = array_merge(explode(',', $entry->getEntitledKusersEdit()), explode(',', $entry->getEntitledKusersPublish()));
			if(in_array($kuserId, $entitledKusers))
				return true; 
	
			// entry that doesn't belong to any category is public
			if(trim($entry->getCategories()) == '')
				return true;
					
			// kuser is set on the category as member
			// this ugly code is temporery - since we have a bug in sphinxCriteria::getAllCriterionFields
			$membersCrit = $c->getNewCriterion ( categoryPeer::MEMBERS , $kuserId, Criteria::EQUAL);
			$membersCrit->addOr($crit);
			$crit = $membersCrit;
			
		}
			
		$c->addAnd(categoryPeer::ID, explode(',', $entry->getCategoriesIds()), Criteria::IN);
		$c->addAnd($crit);
		
		//remove default FORCED criteria since categories that has display in search = public - doesn't mean that all of their entries are public
		KalturaCriterion::disableTag(KalturaCriterion::TAG_ENTITLEMENT_CATEGORY);
		$category = categoryPeer::doSelectOne($c);
		KalturaCriterion::restoreTag(KalturaCriterion::TAG_ENTITLEMENT_CATEGORY);

		if($category)
			return true;
		
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
	public static function initEntitlementEnforcement()
	{
		$partnerId = kCurrentContext::$partner_id ? kCurrentContext::$partner_id : kCurrentContext::$ks_partner_id; 
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
			
		if (self::$entitlementEnforcement)
		{
			KalturaCriterion::enableTag(KalturaCriterion::TAG_ENTITLEMENT_ENTRY);
			KalturaCriterion::enableTag(KalturaCriterion::TAG_ENTITLEMENT_CATEGORY);
		}
	}
	
	public static function getPrivacyContextSearch()
	{
		$privacyContextSearch = array();
			
		$ks = ks::fromSecureString(kCurrentContext::$ks);
		if(!$ks)
			return array(self::PRIVACY_CONTEXT_PREFIX . self::DEFAULT_CONTEXT.
						 ' ' . PrivacyType::ALL . ' ' . self::PRIVACY_CONTEXT_PREFIX . self::DEFAULT_CONTEXT);
			
		$ksPrivacyContexts = $ks->getPrivacyContext();
		
		if(is_null($ksPrivacyContexts))
			$ksPrivacyContexts = self::DEFAULT_CONTEXT;
		
		$ksPrivacyContexts = explode(',', $ksPrivacyContexts);
		
		foreach ($ksPrivacyContexts as $ksPrivacyContext)
		{
			$privacyContextSearch[] = self::PRIVACY_CONTEXT_PREFIX . $ksPrivacyContext . ' ' . PrivacyType::ALL . ' ' . self::PRIVACY_CONTEXT_PREFIX . $ksPrivacyContext;
			
			if (!$ks->isWidgetSession())
				$privacyContextSearch[] = self::PRIVACY_CONTEXT_PREFIX . $ksPrivacyContext . ' ' . PrivacyType::AUTHENTICATED_USERS . ' ' . self::PRIVACY_CONTEXT_PREFIX . $ksPrivacyContext;
		}
		
		return $privacyContextSearch;
	}
	
	public static function getPrivacyContextForEntry(entry $entry)
	{
		$categoriesIds = $entry->getCategoriesIds();
		$categoriesIds = explode(',', $categoriesIds);
		
		$privacyContexts = array();
		$entryPrivacy = null;		
		
		$c = KalturaCriteria::create(categoryPeer::OM_CLASS); 
		$c->add(categoryPeer::ID, explode(',', $entry->getCategoriesIds()), Criteria::IN);
		
		KalturaCriterion::disableTag(KalturaCriterion::TAG_ENTITLEMENT_CATEGORY);
		$categories = categoryPeer::doSelect($c);
		KalturaCriterion::restoreTag(KalturaCriterion::TAG_ENTITLEMENT_CATEGORY);
		
		foreach ($categories as $category)
		{				
			$categoryPrivacy = $category->getPrivacy();
			$categoryPrivacyContexts = $category->getPrivacyContexts();
			if(!$categoryPrivacyContexts)
				$categoryPrivacyContexts = '';
			
			$categoryPrivacyContexts = explode(',', $categoryPrivacyContexts);
			
			foreach ($categoryPrivacyContexts as $categoryPrivacyContext)
			{
				if(!isset($privacyContexts[$categoryPrivacyContext]) || $privacyContexts[$categoryPrivacyContext] > $categoryPrivacy)
					$privacyContexts[$categoryPrivacyContext] = $categoryPrivacy;
			}
		}
		
		//Entry That doesn't assinged to any category is public.
		if (!count($categories))
			$privacyContexts[self::DEFAULT_CONTEXT] = PrivacyType::ALL ;
		
		$entryPrivacyContexts = array();
		foreach ($privacyContexts as $categoryPrivacyContext => $Privacy)
			$entryPrivacyContexts[] = self::PRIVACY_CONTEXT_PREFIX . $categoryPrivacyContext . ' ' . $Privacy . ' ' . self::PRIVACY_CONTEXT_PREFIX . $categoryPrivacyContext;
		
		return $entryPrivacyContexts;
	}
	
	public static function getKsPrivacyContext()
	{
		$ks = ks::fromSecureString(kCurrentContext::$ks);
		if(!$ks)
			return array(self::DEFAULT_CONTEXT);
			
		$ksPrivacyContexts = $ks->getPrivacyContext();
		
		if(is_null($ksPrivacyContexts) || $ksPrivacyContexts = '')
			return array(self::DEFAULT_CONTEXT);
			
		return explode(',', $ksPrivacyContexts);
	}
}
