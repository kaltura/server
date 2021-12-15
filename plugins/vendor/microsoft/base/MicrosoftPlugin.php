<?php

/**
 *@package plugins.Microsoft
 *
 */
class MicrosoftPlugin extends KalturaPlugin
{
    const PLUGIN_NAME = "microsoft";

	/* (non-PHPdoc)
     * @see IKalturaPlugin::getPluginName()
     */
    public static function getPluginName ()
    {
        return self::PLUGIN_NAME;
        
    }
}
