<?php

/**
 * @package plugins.elasticSearch
 * @subpackage lib.entitlement
 */
class kEntryElasticEntitlement extends kBaseElasticEntitlement
{
    
    public static $kuserId = null;
    public static $privacyContext = null;
    public static $privacy = null;
    public static $userEntitlement = false;
    public static $userCategoryToEntryEntitlement = false;
    public static $entriesDisabledEntitlement = array();
    public static $publicEntries = false; //active + pending
    public static $publicActiveEntries = false; //active
    public static $parentEntitlement = false;
    public static $entryInSomeCategoryNoPC = false; //active + pending
    
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
        self::$kuserId = self::getKuserIdForEntitlement(self::$partnerId, self::$kuserId, self::$ks);
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

            self::$userCategoryToEntryEntitlement = true;
        }
    }

    private static function getKuserIdForEntitlement($partnerId, $kuserId = null, $ks = null)
    {
        if($ks && !$kuserId)
        {
            $kuser = kuserPeer::getKuserByPartnerAndUid($partnerId, kCurrentContext::$ks_uid, true);
            if($kuser)
                $kuserId = $kuser->getId();
        }

        return $kuserId;
    }
}
