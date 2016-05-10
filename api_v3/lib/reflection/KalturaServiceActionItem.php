<?php
/**
 * @package api
 * @subpackage v3
 */
class KalturaServiceActionItem
{
    /**
     * @var string
     */
    public $serviceId;
    
    /**
     * @var string
     */
    public $serviceClass;
    
    /**
     * @var KalturaDocCommentParser
     */
    public $serviceInfo;
    
    /**
     * @var array
     */
    public $actionMap;
    
    public static function cloneItem (KalturaServiceActionItem $item)
    {
        $serviceActionItem = new KalturaServiceActionItem();
        $serviceActionItem->serviceId = $item->serviceId;
        $serviceActionItem->serviceClass = $item->serviceClass;
        $serviceActionItem->serviceInfo = $item->serviceInfo;
        $serviceActionItem->actionMap = $item->actionMap;
        return $serviceActionItem;
    }

}
