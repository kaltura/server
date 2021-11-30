<?php
/**
 * @package plugins.emailNotification
 * @subpackage api.enums
 */
class KalturaEmailNotificationErrors extends KalturaErrors
{
	const INVALID_FILTER_PROPERTY = "INVALID_FILTER_PROPERTY;PROP_VALUE;The value of property [@PROP_VALUE@] cannot be set from this context";
	const DYNAMIC_EMAIL_CONTENT_TEMPLATE_FAULT = "DYNAMIC_EMAIL_CONTENT_TEMPLATE_FAULT;;One of the mandatory contents for the dynamic Email is missing";
}