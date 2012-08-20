<?php
/**
 * @package plugins.audit
 * @subpackage api.enums
 */
class KalturaAuditTrailAction extends KalturaStringEnum
{
	const CREATED = 'CREATED';
	const COPIED = 'COPIED';
	const CHANGED = 'CHANGED';
	const DELETED = 'DELETED';
	const VIEWED = 'VIEWED';
	const CONTENT_VIEWED = 'CONTENT_VIEWED';
	const FILE_SYNC_CREATED = 'FILE_SYNC_CREATED';
	const RELATION_ADDED = 'RELATION_ADDED';
	const RELATION_REMOVED = 'RELATION_REMOVED';
}
