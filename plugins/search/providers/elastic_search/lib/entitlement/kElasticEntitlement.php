<?php
/**
 * @package plugins.elasticSearch
 * @subpackage lib.entitlement
 */

class kElasticEntitlement
{
    public static $partnerId;
    public static $kuserId = null;
    public static $privacyContext = null;
    public static $privacy = null;
    public static $userEntitlement = false;
    public static $userCategoryToEntryEntitlement = false;
    public static $entriesDisabledEntitlement = array();
    public static $publicEntries = false; //active + pending
    public static $publicActiveEntries = false; //active
    public static $parentEntitlement = false;
    public static $isInitialized = false;
    public static $entryInSomeCategoryNoPC = false; //active + pending

    public static function init()
    {
        if(!self::$isInitialized)
            self::initialize();
    }

    protected static function initialize()
    {
        $ks = ks::fromSecureString(kCurrentContext::$ks);
        self::$partnerId = kCurrentContext::$partner_id ? kCurrentContext::$partner_id : kCurrentContext::$ks_partner_id;
        $partner = PartnerPeer::retrieveByPK(self::$partnerId);

        //disable the entitlement checks for partner
        if(!$partner->getDefaultEntitlementEnforcement())
            return;//todo

        self::initializeParentEntitlement();
        self::initializeDisableEntitlement($ks);
        self::$kuserId = self::getKuserIdForEntitlement(self::$partnerId, self::$kuserId, $ks); //todo - kuser is null maybe allow to pass as param
        self::initializeUserEntitlement($ks);

        if($ks)
            self::$privacyContext = $ks->getPrivacyContext();

        self::initializePublicEntryEntitlement($ks); //todo - add active category entitlement

        self::initializeUserCategoryEntryEntitlement($ks);


        self::$isInitialized = true;
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
        if($ks && self::$kuserId) //todo to check if kuserId ==''
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
