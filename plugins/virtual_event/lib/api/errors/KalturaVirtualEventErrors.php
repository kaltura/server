<?php

/**
 * @package plugins.virtualEvent
 * @subpackage api.errors
 */
class KalturaVirtualEventErrors extends KalturaErrors
{
	const VIRTUAL_EVENT_NOT_FOUND = "VIRTUAL_EVENT_NOT_FOUND;ID;Virtual Event [@ID@] not found";
	const VIRTUAL_EVENT_NOT_ACTIVE = "VIRTUAL_EVENT_NOT_ACTIVE;ID;Virtual Event [@ID@] not active";
	const VIRTUAL_EVENT_DISABLED = "VIRTUAL_EVENT_DISABLED;Virtual Event plugin disabled";
}