<?php
/**
 * @package plugins.integration
 * @subpackage api.errors
 */
class KalturaIntegrationErrors extends KalturaErrors
{
	const INTEGRATION_DISPATCH_FAILED = "INTEGRATION_DISPATCH_FAILED;TYPE;Dispatching integration type [@TYPE@] failed";
	const INTEGRATION_NOTIFY_FAILED = "INTEGRATION_NOTIFY_FAILED;;Notifying failed";
}