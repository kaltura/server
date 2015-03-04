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
}