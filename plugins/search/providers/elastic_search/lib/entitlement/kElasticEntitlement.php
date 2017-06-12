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
    public static $publicEntries = false;
    public static $parentEntitlement = false;
    public static $isInitialized = false;

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

        if(!(PermissionPeer::isValidForPartner(PermissionName::FEATURE_DISABLE_PARENT_ENTRY_SECURITY_INHERITANCE, self::$partnerId)))
        {
            //todo - we need to add entitlement check on the parent entry if exist
            self::$parentEntitlement = true;
        }

        if($ks && count($ks->getDisableEntitlementForEntry()))
        {
            //disable entitlement for entries
            $entries = $ks->getDisableEntitlementForEntry();
            self::$entriesDisabledEntitlement = $entries;
        }

        self::$kuserId = self::getKuserIdForEntitlement(self::$partnerId, self::$kuserId, $ks); //todo - kuser is null maybe allow to pass as param

        if($ks && self::$kuserId) //todo to check if kuserId ==''
        {
            self::$userEntitlement = true;
        }

        if(!$ks)
        {
            self::$publicEntries = true;
        }
        
        if($ks)
            self::$privacyContext = $ks->getPrivacyContext();

        if(self::$kuserId)
        {
            self::$privacy = array(category::formatPrivacy(PrivacyType::ALL, self::$partnerId));
            if($ks && !$ks->isAnonymousSession())
                $privacy[] = category::formatPrivacy(PrivacyType::AUTHENTICATED_USERS, self::$partnerId);

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
