<?php

class KalturaApplicationLinksPlugin extends KalturaPlugin implements IKalturaApplicationPartialView, IKalturaAdminConsolePages
{
    const PLUGIN_NAME = 'KalturaApplicationLinks';
    const ROOT_LABEL = "App Links";

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
        $applicationPages = array();
	    
        $applicationLinks = Zend_Registry::get('config')->applicationLinks;
        foreach ($applicationLinks as $applicationLink => $value)
        {
            $ctorName = $applicationLink."AdminAction";
            $applicationPages[] = new $ctorName();
        }
        
        return $applicationPages;
    }
}
