<?php
class KalturaAuditTrailFileSyncCreateInfo extends KalturaAuditTrailInfo
{
	/**
	 * @var string
	 */
	public $version;

	/**
	 * @var KalturaAuditTrailFileSyncSubType
	 */
	public $objectSubType;

	/**
	 * @var int
	 */
	public $dc;

	/**
	 * @var bool
	 */
	public $original;

	/**
	 * @var KalturaAuditTrailFileSyncType
	 */
	public $fileType;
}
