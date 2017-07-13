<?php

/**
 * Sending beacons on various objects
 * @package plugins.beacon
 */

class BeaconPlugin extends KalturaPlugin implements IKalturaServices, IKalturaPermissions {
    const PLUGIN_NAME = "beacon";

    /* (non-PHPdoc)
 * @see IKalturaServices::getServicesMap()
 */
    public static function getServicesMap ()
    {
        $map = array(
            'beacon' => 'BeaconService',
        );
        return $map;
    }

    /* (non-PHPdoc)
     * @see IKalturaPermissions::isAllowedPartner()
     */
    public static function isAllowedPartner($partnerId)
    {
        return true;
    }


    /* (non-PHPdoc)
     * @see IKalturaPlugin::getPluginName()
     */
    public static function getPluginName ()
    {
        return self::PLUGIN_NAME;
    }


}