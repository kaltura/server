<?php
/**
 * @package plugins.webcast
 */
class WebcastPlugin extends KalturaPlugin implements IKalturaServices {

    const PLUGIN_NAME = "webCasting";

    public static function getPluginName() {
        return self::PLUGIN_NAME;
    }

    public static function getServicesMap()
    {
        $map = array(
            'webCasting' => 'WebCastingService',
        );
        return $map;
    }
}

    

