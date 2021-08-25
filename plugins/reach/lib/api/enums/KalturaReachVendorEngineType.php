<?php
/**
 * @package plugins.reach
 * @subpackage api.enum
 * @see ReachVendorEngineType
 */

class KalturaReachVendorEngineType extends KalturaDynamicEnum implements ReachVendorEngineType
{

    /**
     * @inheritDoc
     */
    public static function getEnumClass()
    {
        return 'ReachVendorEngineType';
    }
}