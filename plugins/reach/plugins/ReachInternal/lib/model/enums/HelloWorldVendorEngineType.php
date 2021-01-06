<?php
/**
 * @package plugins.reachInternal
 * @subpackage lib
 */

class HelloWorldVendorEngineType implements IKalturaPluginEnum, ReachVendorEngineType
{

    const HELLO_WORLD = 'HELLO_WORLD';

    /**
     * @inheritDoc
     */
    public static function getAdditionalValues()
    {
        return array(
            'HELLO_WORLD' => self::HELLO_WORLD,
        );
    }

    /**
     * @inheritDoc
     */
    public static function getAdditionalDescriptions()
    {
        return array(
            ReachInternalPlugin::getApiValue(self::HELLO_WORLD) => 'Hello-World engine type for testing',
        );
    }
}