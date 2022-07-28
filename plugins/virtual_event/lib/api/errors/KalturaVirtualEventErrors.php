<?php

/**
 * @package plugins.virtualEvent
 * @subpackage api.errors
 */
class KalturaVirtualEventErrors extends KalturaErrors
{
	const VIRTUAL_EVENT_NOT_FOUND = "VIRTUAL_EVENT_NOT_FOUND;ID;Virtual Event [@ID@] not found";
	const VIRTUAL_EVENT_NOT_ACTIVE = "VIRTUAL_EVENT_NOT_ACTIVE;ID;Virtual Event [@ID@] not active";
	const VIRTUAL_EVENT_PLUGIN_DISABLED = "VIRTUAL_EVENT_PLUGIN_DISABLED;;Virtual Event plugin disabled";
	const VIRTUAL_EVENT_INVALID_REGISTRATION_FORM_SCHEMA = "VIRTUAL_EVENT_INVALID_REGISTRATION_FORM_SCHEMA;;Invalid JSON Registration Form Schema, please check JSON";
}
