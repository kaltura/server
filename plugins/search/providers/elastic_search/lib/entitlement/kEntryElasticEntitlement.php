<?php

/**
 * @package plugins.elasticSearch
 * @subpackage lib.entitlement
 */
class kEntryElasticEntitlement extends kBaseElasticEntitlement
{
    
    public static $privacyContext = null;
    public static $privacy = null;
    public static $userEntitlement = false;
    public static $userCategoryToEntryEntitlement = false;
    public static $entriesDisabledEntitlement = array();
    public static $publicEntries = false; //active + pending
    public static $publicActiveEntries = false; //active
    public static $parentEntitlement = false;
    public static $entryInSomeCategoryNoPC = false; //active + pending
    public static $filteredCategoryIds = array();

    protected static $entitlementContributors = array(
        'kElasticEntryDisableEntitlementDecorator',
        'kElasticPublicEntriesEntitlementDecorator',
        'kElasticUserCategoryEntryEntitlementDecorator',
        'kElasticUserEntitlementDecorator',
    );

    protected static function initialize()
    {
        parent::initialize();

        //check if we need to enforce entitlement
        if(!self::shouldEnforceEntitlement())
            return;

        self::initializeParentEntitlement();
        self::initializeDisableEntitlement(self::$ks);
        self::initializeUserEntitlement(self::$ks);

        if(self::$ks)
            self::$privacyContext = self::$ks->getPrivacyContext();

        self::initializePublicEntryEntitlement(self::$ks);
        self::initializeUserCategoryEntryEntitlement(self::$ks);
        
        self::$isInitialized = true;
    }

    private static function shouldEnforceEntitlement()
    {
        return kEntitlementUtils::getEntitlementEnforcement();
    }

    private static function initializeParentEntitlement()
    {
        if(!(PermissionPeer::isValidForPartner(PermissionName::FEATURE_DISABLE_PARENT_ENTRY_SECURITY_INHERITANCE, self::$partnerId)))
        {
            //we need to add entitlement check on the parent
            self::$parentEntitlement = true;
        }
    }

    private static function initializeDisableEntitlement($ks)
    {
        if($ks && count($ks->getDisableEntitlementForEntry()))
        {
            //disable entitlement for entries
            $entries = $ks->getDisableEntitlementForEntry();
            self::$entriesDisabledEntitlement = $entries;
        }
    }

    private static function initializeUserEntitlement($ks)
    {
        if($ks && self::$kuserId)
        {
            self::$userEntitlement = true;
        }
    }

    private static function initializePublicEntryEntitlement($ks)
    {
        if(!$ks)
        {
            self::$publicActiveEntries = true; //add entries that are not in any active category
        }
        else //ks
        {
            if(!PermissionPeer::isValidForPartner(PermissionName::FEATURE_DISABLE_CATEGORY_LIMIT, self::$partnerId) && !self::$privacyContext)
                self::$publicEntries = true; //return entries that are not in any active/pending category
        }
    }

    private static function initializeUserCategoryEntryEntitlement($ks)
    {
        if(PermissionPeer::isValidForPartner(PermissionName::FEATURE_DISABLE_CATEGORY_LIMIT, self::$partnerId))
        {
            if(!self::$privacyContext)//add entries that are in some category and doesnt have pc
                self::$entryInSomeCategoryNoPC = true;
        }

        if(self::$kuserId)
        {
            $privacy = array(category::formatPrivacy(PrivacyType::ALL, self::$partnerId));
            if($ks && !$ks->isAnonymousSession())
                $privacy[] = category::formatPrivacy(PrivacyType::AUTHENTICATED_USERS, self::$partnerId);

            self::$privacy = $privacy;
            self::$filteredCategoryIds = array();
            self::$userCategoryToEntryEntitlement = true;
        }
    }

    public static function setFilteredCategoryIds(ESearchOperator $eSearchOperator, $objectId)
    {
        if($eSearchOperator->getOperator() != ESearchOperatorType::AND_OP)
            return;

        $searchItems = $eSearchOperator->getSearchItems();
        $filteredCategoryIds = array();
        $filteredEntryId = $objectId ? array($objectId) : array();
        foreach ($searchItems as $searchItem)
        {
            $filteredObjectId = $searchItem->getFilteredObjectId();
            if ($filteredObjectId)
            {
                $filteredEntryId[] = $filteredObjectId;
            }
            $FilteredCategoryId = $searchItem->getFilteredCategoryId();
            if ($FilteredCategoryId)
            {
                $filteredCategoryIds[] = $FilteredCategoryId;
            }
        }

        $filteredCategoriesByEntryId = self::getCategoryIdsForEntryId($filteredEntryId);
        $filteredCategoryIds = array_merge($filteredCategoryIds, $filteredCategoriesByEntryId);
        self::$filteredCategoryIds = $filteredCategoryIds;
    }

    protected static function getCategoryIdsForEntryId($filteredEntryId)
    {
        $filteredCategoryIds = array();
        $filteredEntriesIds = array_unique($filteredEntryId);
        $filteredEntriesIds = array_values($filteredEntriesIds);
        if (count($filteredEntriesIds) == 1)
        {
            $categoryEntries = categoryEntryPeer::selectByEntryId($filteredEntriesIds[0]);
            foreach ($categoryEntries as $categoryEntry)
            {
                $filteredCategoryIds[] = $categoryEntry->getCategoryId();
            }
        }
        return $filteredCategoryIds;
    }

}
