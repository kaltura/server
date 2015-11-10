<?php

class CollaajUpdatePlugin extends KalturaPlugin implements IKalturaServices {

    const PLUGIN_NAME = "CollaajUpdate";

    public static function getPluginName() {
        return self::PLUGIN_NAME;
    }

    public static function getServicesMap()
    {
        $map = array(
            'collaajini' => 'CollaajUpdateService',
        );
        return $map;
    }
}

    

