<?php
/**
 * @package plugins.pushNotification
 * @subpackage api.objects
*/
class KalturaPushNotificationTemplate extends KalturaEventNotificationTemplate
{
    public function __construct()
    {
        $this->type = PushNotificationPlugin::getApiValue(PushNotificationTemplateType::PUSH);
    }


    /* (non-PHPdoc)
     * @see KalturaObject::toObject()
     */
    public function toObject($dbObject = null, $propertiesToSkip = array())
    {
        if(is_null($dbObject))
            $dbObject = new PushNotificationTemplate();
        	
        return parent::toObject($dbObject, $propertiesToSkip);
    }
}