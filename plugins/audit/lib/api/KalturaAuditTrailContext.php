<?php
/**
 * @package plugins.audit
 * @subpackage api.enums
 */
class KalturaAuditTrailContext extends KalturaEnum
{
	const CLIENT = -1;
	const SCRIPT = 0;
	const PS2 = 1;
	const API_V3 = 2;
}
