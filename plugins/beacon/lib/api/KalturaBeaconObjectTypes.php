<?php
/**
 * Created by IntelliJ IDEA.
 * User: moshe.maor
 * Date: 7/16/2017
 * Time: 6:14 PM
 */
/**
 * @package plugins.beacon
 * @subpackage api.enum
 */
class KalturaBeaconObjectTypes extends KalturaDynamicEnum implements BeaconObjectTypes
{
    public static function getEnumClass()
    {
        return 'BeaconObjectTypes';
    }
}