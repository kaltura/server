<?php

/**
 * @package plugins.eventNotification
 * @subpackage api.errors
 */
class KalturaEventNotificationErrors extends KalturaErrors
{
	const EVENT_NOTIFICATION_TEMPLATE_NOT_FOUND = "EVENT_NOTIFICATION_TEMPLATE_NOT_FOUND,Event notification template id [%s] not found";
	
	const EVENT_NOTIFICATION_DISPATH_DISABLED = "EVENT_NOTIFICATION_DISPATH_DISABLED,Dispatching event notification template id [%s] is not allowed";
	
	const EVENT_NOTIFICATION_DISPATH_FAILED = "EVENT_NOTIFICATION_DISPATH_FAILED,Dispatching event notification template id [%s] failed";
}