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
	const TYPE_SEPERATOR = "TYPE";
	const ENTRY_PRIVACY_CONTEXT = 'ENTRYPC';
	const PARTNER_ID_PREFIX = 'pid';

	protected static $initialized = false;
	protected static $entitlementEnforcement = false;
	protected static $entitlementForced = null;
	protected static $privacyContextSearch = null;
	protected static $categoryModeration = false;

	public static function getDefaultContextString( $partnerId )
	{
		return self::getPartnerPrefix($partnerId) . self::DEFAULT_CONTEXT;
	}

	public static function getPartnerPrefix($partnerId)
	{
		return kEntitlementUtils::PARTNER_ID_PREFIX . $partnerId;
	}

	public static function addPrivacyContextsPrefix($privacyContextsArray, $partnerId )
	{
		if ( is_null($privacyContextsArray) || is_null($partnerId))
		{
			KalturaLog::err("can't handle privacy context for privacyContextsArray: $privacyContextsArray and partnerId: $partnerId.");
			return $privacyContextsArray;
		}
		$prefix = self::getPartnerPrefix($partnerId);

		foreach ($privacyContextsArray as &$value)
		{
			$value = $prefix . $value;
		}

		return $privacyContextsArray;

	}

	public static function getEntitlementEnforcement()
	{
		return self::$entitlementEnforcement;
	}

	public static function getCategoryModeration ()
	{
		return self::$categoryModeration;
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
		if($entry->getSecurityParentId())
		{
			$entry = $entry->getParentEntry();
			if(!$entry)
			{
				KalturaLog::log('Parent entry not found, cannot validate entitlement');
				return false;
			}
		}

		$ks = ks::fromSecureString(kCurrentContext::$ks);

		if(self::$entitlementForced === false)
		{
			KalturaLog::log('Entitlement forced to be disabled');
			return true;
		}

		// entry is entitled when entitlement is disable
		// for actions with no ks - need to check if partner have default entitlement feature enable.
		if(!self::getEntitlementEnforcement() && $ks)
		{
			KalturaLog::log('Entry entitled: entitlement disabled');
			return true;
		}

		$partner = $entry->getPartner();

		if(!$ks && !$partner->getDefaultEntitlementEnforcement())
		{
			KalturaLog::info('Entry [' . print_r($entry->getId(), true) . '] entitled: no ks and default is with no enforcement');
			return true;
		}

		if($ks && in_array($entry->getId(), $ks->getDisableEntitlementForEntry()))
		{
			KalturaLog::info('Entry [' . print_r($entry->getId(), true) . '] entitled: ks disble entitlement for this entry');
			return true;
		}

		$kuserId = self::getKuserIdForEntitlement($kuserId, $ks);

		if($ks && $kuserId)
		{
			// kuser is set on the entry as creator or uploader
			if ($kuserId != '' && ($entry->getKuserId() == $kuserId))
			{
				KalturaLog::info('Entry [' . print_r($entry->getId(), true) . '] entitled: ks user is the same as entry->kuserId or entry->creatorKuserId [' . $kuserId . ']');
				return true;
			}

			// kuser is set on the entry entitled users edit or publish
			if($entry->isEntitledKuserEdit($kuserId) || $entry->isEntitledKuserPublish($kuserId) || $entry->isEntitledKuserView($kuserId))
			{
				KalturaLog::info('Entry [' . print_r($entry->getId(), true) . '] entitled: ks user is the same as entry->entitledKusersEdit or entry->entitledKusersPublish or entry->entitledKusersView');
				return true;
			}
		}

		if(!$ks)
		{
			// entry that doesn't belong to any category is public
			//when ks is not provided - the entry is still public (for example - download action)
			$categoryEntry = categoryEntryPeer::retrieveOneActiveByEntryId($entry->getId());
			if(!$categoryEntry)
			{
				KalturaLog::info('Entry [' . print_r($entry->getId(), true) . '] entitled: entry does not belong to any category');
				return true;
			}
		}

		$ksPrivacyContexts = null;
		if($ks)
			$ksPrivacyContexts = $ks->getPrivacyContext();

		$allCategoriesEntry = array();

		if(PermissionPeer::isValidForPartner(PermissionName::FEATURE_DISABLE_CATEGORY_LIMIT, $partner->getId()))
		{
			if(!$ksPrivacyContexts || trim($ksPrivacyContexts) == '')
			{
				$categoryEntry = categoryEntryPeer::retrieveOneByEntryIdStatusPrivacyContextExistance($entry->getId(), array(CategoryEntryStatus::PENDING, CategoryEntryStatus::ACTIVE));
				if($categoryEntry)
				{
					KalturaLog::info('Entry [' . print_r($entry->getId(), true) . '] entitled: entry belongs to public category and privacy context on the ks is not set');
					return true;
				}
			}
			else
				$allCategoriesEntry = categoryEntryPeer::retrieveActiveAndPendingByEntryIdAndPrivacyContext($entry->getId(), $ksPrivacyContexts);
		}
		else
		{
			$allCategoriesEntry = categoryEntryPeer::retrieveActiveAndPendingByEntryId($entry->getId());
			if($ks && (!$ksPrivacyContexts || trim($ksPrivacyContexts) == '') && !count($allCategoriesEntry))
			{
				// entry that doesn't belong to any category is public
				KalturaLog::info('Entry [' . print_r($entry->getId(), true) . '] entitled: entry does not belong to any category and privacy context on the ks is not set');
				return true;
			}
		}

		return self::isMemberOfCategory($allCategoriesEntry, $entry, $partner, $kuserId, $ks, $ksPrivacyContexts);
	}

	private static function getKuserIdForEntitlement($kuserId = null, $ks = null)
	{
		if($ks && !$kuserId)
		{
			$partnerId = kCurrentContext::$partner_id ? kCurrentContext::$partner_id : kCurrentContext::$ks_partner_id;
			$kuser = kuserPeer::getKuserByPartnerAndUid($partnerId, kCurrentContext::$ks_uid, true);
			if($kuser)
				$kuserId = $kuser->getId();
		}

		return $kuserId;
	}

	private static function isMemberOfCategory($allCategoriesEntry, entry $entry, Partner $partner, $kuserId = null, $ks = null, $ksPrivacyContexts = null)
	{
		$categories = array();
		foreach($allCategoriesEntry as $categoryEntry)
			$categories[] = $categoryEntry->getCategoryId();

		//if entry doesn't belong to any category.
		$categories[] = category::CATEGORY_ID_THAT_DOES_NOT_EXIST;

		$c = KalturaCriteria::create(categoryPeer::OM_CLASS);
		$c->add(categoryPeer::ID, $categories, Criteria::IN);

		$privacy = array(category::formatPrivacy(PrivacyType::ALL, $partner->getId()));
		if($ks && !$ks->isAnonymousSession())
			$privacy[] = category::formatPrivacy(PrivacyType::AUTHENTICATED_USERS, $partner->getId());

		$crit = $c->getNewCriterion (categoryPeer::PRIVACY, $privacy, Criteria::IN);

		if($ks)
		{
			if (!$ksPrivacyContexts || trim($ksPrivacyContexts) == '')
				$ksPrivacyContexts = self::getDefaultContextString( $partner->getId());
			else
			{
				$ksPrivacyContexts = explode(',', $ksPrivacyContexts);
				$ksPrivacyContexts = self::addPrivacyContextsPrefix( $ksPrivacyContexts, $partner->getId() );
			}

			$c->add(categoryPeer::PRIVACY_CONTEXTS, $ksPrivacyContexts, KalturaCriteria::IN_LIKE);

			// kuser is set on the category as member
			// this ugly code is temporery - since we have a bug in sphinxCriteria::getAllCriterionFields
			if($kuserId)
			{
				// get the groups that the user belongs to in case she is not associated to the category directly
				$kgroupIds = KuserKgroupPeer::retrieveKgroupIdsByKuserId($kuserId);
				$kgroupIds[] = $kuserId;
				$membersCrit = $c->getNewCriterion ( categoryPeer::MEMBERS , $kgroupIds, KalturaCriteria::IN_LIKE);
				$membersCrit->addOr($crit);
				$crit = $membersCrit;
			}
		}
		else
		{
			//no ks = set privacy context to default.
			$c->add(categoryPeer::PRIVACY_CONTEXTS, array( self::getDefaultContextString( $partner->getId() )) , KalturaCriteria::IN_LIKE);
		}

		$c->addAnd($crit);

		//remove default FORCED criteria since categories that has display in search = public - doesn't mean that all of their entries are public
		KalturaCriterion::disableTag(KalturaCriterion::TAG_ENTITLEMENT_CATEGORY);
		$category = categoryPeer::doSelectOne($c);
		KalturaCriterion::restoreTag(KalturaCriterion::TAG_ENTITLEMENT_CATEGORY);

		if($category)
		{
			KalturaLog::info('Entry [' . print_r($entry->getId(), true) . '] entitled: ks user is a member of this category or category privacy is set to public of authenticated');
			return true;
		}

		KalturaLog::info('Entry [' . print_r($entry->getId(), true) . '] not entitled');
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
		self::$entitlementForced = $enableEntit;

		if(is_null($partnerId))
			$partnerId = kCurrentContext::getCurrentPartnerId();

		if(is_null($partnerId) || $partnerId == Partner::BATCH_PARTNER_ID)
			return;

		$partner = PartnerPeer::retrieveByPK($partnerId);
		if (!$partner)
			return;

		$ks = null;
		$ksString = kCurrentContext::$ks ? kCurrentContext::$ks : '';
		if ($ksString != '') // for actions with no KS or when creating ks.
		{
			$ks = ks::fromSecureString($ksString);
		}

		self::initCategoryModeration($ks);

		if(!PermissionPeer::isValidForPartner(PermissionName::FEATURE_ENTITLEMENT, $partnerId))
			return;

		$partnerDefaultEntitlementEnforcement = $partner->getDefaultEntitlementEnforcement();

		// default entitlement scope is true - enable.
		if(is_null($partnerDefaultEntitlementEnforcement))
			$partnerDefaultEntitlementEnforcement = true;

		self::$entitlementEnforcement = $partnerDefaultEntitlementEnforcement;

		if ($ks) // for actions with no KS or when creating ks.
		{
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

	public static function getPrivacyForKs($partnerId)
	{
		$ks = ks::fromSecureString(kCurrentContext::$ks);
		if(!$ks || $ks->isAnonymousSession())
			return array(category::formatPrivacy(PrivacyType::ALL, $partnerId));

		return array(category::formatPrivacy(PrivacyType::ALL, $partnerId),
			category::formatPrivacy(PrivacyType::AUTHENTICATED_USERS, $partnerId));
	}

	public static function getPrivacyContextSearch()
	{
		$partnerId = kCurrentContext::$partner_id ? kCurrentContext::$partner_id : kCurrentContext::$ks_partner_id;

		if (self::$privacyContextSearch)
			return self::$privacyContextSearch;

		$privacyContextSearch = array();

		$ks = ks::fromSecureString(kCurrentContext::$ks);
		if(!$ks)
			return array( self::getDefaultContextString( $partnerId ) . self::TYPE_SEPERATOR . PrivacyType::ALL);

		$ksPrivacyContexts = $ks->getPrivacyContext();

		if(is_null($ksPrivacyContexts))
		{   // setting $ksPrivacyContexts only with DEFAULT_CONTEXT string (to resolve conflicts)
			// since prefix will be add in the addPrivacyContextsPrefix bellow
			$ksPrivacyContexts = self::DEFAULT_CONTEXT;
		}

		$ksPrivacyContexts = explode(',', $ksPrivacyContexts);

		foreach ($ksPrivacyContexts as $ksPrivacyContext)
		{
			$privacyContextSearch[] = $ksPrivacyContext . self::TYPE_SEPERATOR . PrivacyType::ALL;

			if (!$ks->isAnonymousSession())
				$privacyContextSearch[] = $ksPrivacyContext . self::TYPE_SEPERATOR  . PrivacyType::AUTHENTICATED_USERS;
		}

		self::$privacyContextSearch = self::addPrivacyContextsPrefix( $privacyContextSearch, $partnerId );

		return self::$privacyContextSearch;
	}

	public static function setPrivacyContextSearch($privacyContextSearch)
	{
		self::$privacyContextSearch = array($privacyContextSearch . self::TYPE_SEPERATOR . PrivacyType::ALL);
	}

	public static function getPrivacyContextForEntry(entry $entry)
	{
		$privacyContexts = array();

		if(PermissionPeer::isValidForPartner(PermissionName::FEATURE_DISABLE_CATEGORY_LIMIT, $entry->getPartnerId()))
			$privacyContexts = self::getPrivacyContextsByCategoryEntries($entry);
		else
			$privacyContexts = self::getPrivacyContextsByAllCategoryIds($entry);

		//Entry That doesn't assinged to any category is public.
		if (!count($privacyContexts))
			$privacyContexts[self::DEFAULT_CONTEXT] = PrivacyType::ALL ;

		$entryPrivacyContexts = array();
		foreach ($privacyContexts as $categoryPrivacyContext => $Privacy)
			$entryPrivacyContexts[] = $categoryPrivacyContext . self::TYPE_SEPERATOR . $Privacy;

		KalturaLog::info('Privacy by context: ' . print_r($entryPrivacyContexts,true));

		return $entryPrivacyContexts;
	}

	private static function getCategoriesByIds($categoriesIds)
	{
		$c = KalturaCriteria::create(categoryPeer::OM_CLASS);
		KalturaCriterion::disableTag(KalturaCriterion::TAG_ENTITLEMENT_CATEGORY);
		$c->add(categoryPeer::ID, $categoriesIds, Criteria::IN);
		KalturaCriterion::restoreTag(KalturaCriterion::TAG_ENTITLEMENT_CATEGORY);
		$c->dontCount();

		KalturaCriterion::disableTag(KalturaCriterion::TAG_ENTITLEMENT_CATEGORY);
		$categories = categoryPeer::doSelect($c);
		KalturaCriterion::restoreTag(KalturaCriterion::TAG_ENTITLEMENT_CATEGORY);

		return $categories;
	}

	private static function getPrivacyContextsByAllCategoryIds(entry $entry)
	{
		$privacyContexts = array();

		$allCategoriesIds = $entry->getAllCategoriesIds(true);
		if (count($allCategoriesIds))
		{
			$categories = self::getCategoriesByIds($allCategoriesIds);
			foreach ($categories as $category)
			{
				$categoryPrivacy = $category->getPrivacy();
				$categoryPrivacyContexts = $category->getPrivacyContexts();
				if($categoryPrivacyContexts)
				{
					$categoryPrivacyContexts = explode(',', $categoryPrivacyContexts);

					foreach ($categoryPrivacyContexts as $categoryPrivacyContext)
					{
						if(trim($categoryPrivacyContext) == '')
							$categoryPrivacyContext = self::DEFAULT_CONTEXT;

						if(!isset($privacyContexts[$categoryPrivacyContext]) || $privacyContexts[$categoryPrivacyContext] > $categoryPrivacy)
							$privacyContexts[trim($categoryPrivacyContext)] = $categoryPrivacy;
					}
				}
				else
				{
					$privacyContexts[self::DEFAULT_CONTEXT] = PrivacyType::ALL;
				}
			}
		}

		return $privacyContexts;
	}

	private static function getPrivacyContextsByCategoryEntries(entry $entry)
	{
		$privacyContexts = array();
		$categoriesIds = array();

		//get category entries that have privacy context
		$categoryEntries = categoryEntryPeer::retrieveByEntryIdStatusPrivacyContextExistance($entry->getId(), null, true);
		foreach ($categoryEntries as $categoryEntry)
		{
			$categoriesIds[] = $categoryEntry->getCategoryId();
		}

		$categories = self::getCategoriesByIds($categoriesIds);
		foreach ($categories as $category)
		{
			$categoryPrivacy = $category->getPrivacy();
			$categoryPrivacyContext = $category->getPrivacyContexts();
			if(!isset($privacyContexts[$categoryPrivacyContext]) || $privacyContexts[$categoryPrivacyContext] > $categoryPrivacy)
				$privacyContexts[trim($categoryPrivacyContext)] = $categoryPrivacy;
		}

		$noPrivacyContextCategory = categoryEntryPeer::retrieveOneByEntryIdStatusPrivacyContextExistance($entry->getId());
		if($noPrivacyContextCategory)
			$privacyContexts[ self::DEFAULT_CONTEXT ] = PrivacyType::ALL;

		return $privacyContexts;
	}

	public static function getEntitledKuserByPrivacyContext()
	{
		$partnerId = kCurrentContext::$partner_id ? kCurrentContext::$partner_id : kCurrentContext::$ks_partner_id;

		$privacyContextSearch = array();

		$ks = ks::fromSecureString(kCurrentContext::$ks);
		$ksPrivacyContexts = null;
		if ($ks)
			$ksPrivacyContexts = $ks->getPrivacyContext();

		if(is_null($ksPrivacyContexts) || $ksPrivacyContexts == '')
			$ksPrivacyContexts = self::DEFAULT_CONTEXT . $partnerId;

		$ksPrivacyContexts = explode(',', $ksPrivacyContexts);

		$privacyContexts = $ksPrivacyContexts;
		$privacyContexts[] = self::ENTRY_PRIVACY_CONTEXT;

		// get the groups that the user belongs to in case she is not associated to the category directly
		$kuserIds = KuserKgroupPeer::retrieveKgroupIdsByKuserId(kCurrentContext::getCurrentKsKuserId());
		$kuserIds[] = kCurrentContext::getCurrentKsKuserId();
		foreach ($privacyContexts as $privacyContext){
			foreach ( $kuserIds as $kuserId){
				$privacyContextSearch[] = $privacyContext . '_' . $kuserId;
			}
		}

		return $privacyContextSearch;
	}
	public static function getKsPrivacyContext()
	{
		$partnerId = kCurrentContext::$ks_partner_id ? kCurrentContext::$ks_partner_id : kCurrentContext::$partner_id;

		$ks = ks::fromSecureString(kCurrentContext::$ks);
		if(!$ks)
			return array(self::getDefaultContextString( $partnerId ) );

		$ksPrivacyContexts = $ks->getPrivacyContext();
		if(is_null($ksPrivacyContexts) || $ksPrivacyContexts == '')
			return array(self::getDefaultContextString( $partnerId ));
		else
		{
			$ksPrivacyContexts = explode(',', $ksPrivacyContexts);
			$ksPrivacyContexts = self::addPrivacyContextsPrefix( $ksPrivacyContexts, $partnerId);
		}

		return $ksPrivacyContexts;
	}

	/**
	 * Function returns the privacy context(s) found on the KS, if none are found returns array containing DEFAULT_PC
	 */
	public static function getKsPrivacyContextArray()
	{
		$partnerId = kCurrentContext::$ks_partner_id ? kCurrentContext::$ks_partner_id : kCurrentContext::$partner_id;

		$ks = ks::fromSecureString(kCurrentContext::$ks);
		if(!$ks)
			return array(self::DEFAULT_CONTEXT);

		$ksPrivacyContexts = $ks->getPrivacyContext();
		if(is_null($ksPrivacyContexts) || $ksPrivacyContexts == '')
			return array(self::DEFAULT_CONTEXT);

		return explode(',', $ksPrivacyContexts);
	}

	protected static function initCategoryModeration (ks $ks = null)
	{
		if (!$ks)
			return;

		$enableCategoryModeration = $ks->getEnableCategoryModeration();
		if ($enableCategoryModeration)
			self::$categoryModeration = true;
	}

	/**
	 * @param entry $dbEntry
	 * @return bool if current user is admin / entry's owner / co-editor
	 */
	public static function isEntitledForEditEntry( entry $dbEntry )
	{
		if ( kCurrentContext::$is_admin_session || kCurrentContext::getCurrentKsKuserId() == $dbEntry->getKuserId())
			return true;

		return $dbEntry->isEntitledKuserEdit(kCurrentContext::getCurrentKsKuserId());
	}
}
