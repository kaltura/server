<?php
/**
 * @package plugins.pushNotification
 * @subpackage model
*/
class PushNotificationTemplate extends EventNotificationTemplate
{
    public function dispatch(kScope $scope) 
    {
        //TODO send message to queue (rabbitmq)
    }
}