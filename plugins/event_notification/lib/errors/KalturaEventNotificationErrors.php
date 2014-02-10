<?php

/**
 * @package plugins.eventNotification
 * @subpackage api.errors
 */
class KalturaEventNotificationErrors extends KalturaErrors
{
	
	const INVALID_TO_EMAIL = 'INVALID_TO_EMAIL';
	
	const INVALID_CC_EMAIL = 'INVALID_CC_EMAIL';
	
	const EVENT_NOTIFICATION_TEMPLATE_NOT_FOUND = "EVENT_NOTIFICATION_TEMPLATE_NOT_FOUND;ID;Event notification template id [@ID@] not found";

	const EVENT_NOTIFICATION_WRONG_TYPE = "EVENT_NOTIFICATION_WRONG_TYPE;ID,TYPE;Event notification template id [@ID@] is of type [@TYPE@]";
	
	const EVENT_NOTIFICATION_DISPATCH_DISABLED = "EVENT_NOTIFICATION_DISPATCH_DISABLED;ID;Dispatching event notification template id [@ID@] is not allowed";
	
	const EVENT_NOTIFICATION_DISPATCH_FAILED = "EVENT_NOTIFICATION_DISPATCH_FAILED;ID;Dispatching event notification template id [@ID@] failed";
    
    const EVENT_NOTIFICATION_TEMPLATE_DUPLICATE_SYSTEM_NAME = "EVENT_NOTIFICATION_TEMPLATE_DUPLICATE_SYSTEM_NAME;NAME;Event notification template with system name [@NAME@] already exists.";
}