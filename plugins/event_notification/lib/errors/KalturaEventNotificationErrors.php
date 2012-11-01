<?php

/**
 * @package plugins.eventNotification
 * @subpackage api.errors
 */
class KalturaEventNotificationErrors extends KalturaErrors
{
	const EVENT_NOTIFICATION_TEMPLATE_NOT_FOUND = "EVENT_NOTIFICATION_TEMPLATE_NOT_FOUND,Event notification template id [%s] not found";

	const EVENT_NOTIFICATION_WRONG_TYPE = "EVENT_NOTIFICATION_WRONG_TYPE,Event notification template id [%s] is of type [%s]";
	
	const EVENT_NOTIFICATION_DISPATCH_DISABLED = "EVENT_NOTIFICATION_DISPATCH_DISABLED,Dispatching event notification template id [%s] is not allowed";
	
	const EVENT_NOTIFICATION_DISPATCH_FAILED = "EVENT_NOTIFICATION_DISPATCH_FAILED,Dispatching event notification template id [%s] failed";
    
    const EVENT_NOTIFICATION_TEMPLATE_DUPLICATE_SYSTEM_NAME = "EVENT_NOTIFICATION_TEMPLATE_DUPLICATE_SYSTEM_NAME,Event notification template with system name [%s] already exists.";
}