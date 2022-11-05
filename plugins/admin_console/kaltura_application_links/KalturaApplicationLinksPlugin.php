<?php

class KalturaApplicationLinksPlugin extends KalturaPlugin implements IKalturaApplicationPartialView, IKalturaAdminConsolePages{
    const PLUGIN_NAME = 'KalturaApplicationLinks';

    /* (non-PHPdoc)
     * @see IKalturaPlugin::getPluginName()
     */
    public static function getPluginName()
    {
        return self::PLUGIN_NAME;
    }
    /* (non-PHPdoc)
     * @see IKalturaApplicationPartialView::getApplicationPartialViews()
     */
    public static function getApplicationPartialViews($controller, $action)
    {
        return array();
    }

    /* (non-PHPdoc)
     * @see IKalturaAdminConsolePages::getApplicationPages()
     */
    public static function getApplicationPages()
    {
        //todo take list of items from config
        $links = array(
            new SelfserveAdminAction(),
        );
        return $links;
    }
}