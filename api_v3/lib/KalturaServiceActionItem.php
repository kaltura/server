<?php
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
    
    public function getServiceReflector()
    {
    	if(!$this->serviceClass)
    		throw new Exception("Service class [$this->serviceClass] does not exists in service action item [" . print_r($this, true) . "]");
    		
    	return KalturaServiceReflector::constructFromClassName($this->serviceClass);
    }
}