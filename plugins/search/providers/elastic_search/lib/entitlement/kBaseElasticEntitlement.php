<?php

/**
 * @package plugins.elasticSearch
 * @subpackage lib.entitlement
 */
abstract class kBaseElasticEntitlement
{
    public static $isInitialized = false;
    public static $partnerId;
    public static $ks;
    public static $kuserId = null;

    protected static $entitlementContributors = array();

    public static function init()
    {
        if(!self::$isInitialized)
            static::initialize();
    }

    protected static function initialize()
    {
        self::$ks = ks::fromSecureString(kCurrentContext::$ks);
        self::$partnerId = kCurrentContext::$partner_id ? kCurrentContext::$partner_id : kCurrentContext::$ks_partner_id;
        self::$kuserId = self::getKuserIdForEntitlement(self::$partnerId, self::$kuserId, self::$ks);
    }

    public static function getEntitlementContributors()
    {
        return static::$entitlementContributors;
    }

    protected static function getKuserIdForEntitlement($partnerId, $kuserId = null, $ks = null)
    {
        if($ks && !$kuserId)
        {
            $kuser = kuserPeer::getKuserByPartnerAndUid($partnerId, kCurrentContext::$ks_uid, true);
            if ($kuser)
                $kuserId = $kuser->getId();
        }

        return $kuserId;
    }

}
