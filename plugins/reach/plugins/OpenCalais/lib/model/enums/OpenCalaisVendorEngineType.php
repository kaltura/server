<?php
/**
 * @package plugins.openCalaisReachVendor
 * @subpackage lib
 */

class OpenCalaisVendorEngineType implements IKalturaPluginEnum, ReachVendorEngineType
{
    const OPEN_CALAIS = 'OPEN_CALAIS';

    /**
     * @inheritDoc
     */
    public static function getAdditionalValues()
    {
        return array(
            'OPEN_CALAIS' => self::OPEN_CALAIS,
        );
    }

    /**
     * @inheritDoc
     */
    public static function getAdditionalDescriptions()
    {
        return array(
            OpenCalaisReachVendorPlugin::getApiValue(self::OPEN_CALAIS) => 'Open Calais Reach Engine Type',
        );
    }
}