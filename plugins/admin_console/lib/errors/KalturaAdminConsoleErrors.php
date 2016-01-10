<?php
/**
 * @package plugins.admin_console
 * @subpackage api.enums
 */
class KalturaAdminConsoleErrors extends KalturaErrors
{
	const ENTRY_ASSETS_WRONG_STATUS_FOR_RESTORE = "ENTRY_ASSETS_WRONG_STATUS_FOR_RESTORE;ENTRY_ID; Entry [@ENTRY_ID@] or one of its assets are not in status \"DELETED\"";
}