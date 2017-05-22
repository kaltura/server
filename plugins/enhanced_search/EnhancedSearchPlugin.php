<?php
/**
 * @package plugins.enhancedSearch
 */
class EnhancedSearchPlugin extends KalturaPlugin implements IKalturaServices {

    const PLUGIN_NAME = "enhancedSearch";

    public static function getPluginName() {
        return self::PLUGIN_NAME;
    }

    public static function getServicesMap()
    {
        $map = array(
            'enhancedSearch' => 'EnhancedSearchService',
        );
        return $map;
    }
}

    

