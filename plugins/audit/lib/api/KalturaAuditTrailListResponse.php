<?php
class KalturaAuditTrailListResponse extends KalturaObject
{
	/**
	 * @var KalturaAuditTrailArray
	 * @readonly
	 */
	public $objects;

	/**
	 * @var int
	 * @readonly
	 */
	public $totalCount;
}