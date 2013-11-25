<?php
/**
 * @package plugins.audit
 * @subpackage errors
 */
class AuditTrailErrors extends KalturaErrors
{
	const AUDIT_TRAIL_DISABLED = "AUDIT_TRAIL_DISABLED;PID,OBJ_TYPE,ACTION;audit trail disabled for this partner [@PID@] object [@OBJ_TYPE@] action [@ACTION@]";
}