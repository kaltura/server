<?php
/**
 * @package plugins.pushNotification
 * @subpackage model
*/
class PushNotificationTemplate extends EventNotificationTemplate
{
    public function __construct()
    {
        $this->setType(PushNotificationPlugin::getPushNotificationTemplateTypeCoreValue(PushNotificationTemplateType::PUSH));
        parent::__construct();
    }
        
    public function dispatch(kScope $scope) 
    {
        //TODO send message to queue (rabbitmq)
    }
}