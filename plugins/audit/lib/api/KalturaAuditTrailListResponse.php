<?php
/**
 * @package plugins.audit
 * @subpackage api.objects
 */
class KalturaAuditTrailListResponse extends KalturaListResponse
{
	/**
	 * @var KalturaAuditTrailArray
	 * @readonly
	 */
	public $objects;
}