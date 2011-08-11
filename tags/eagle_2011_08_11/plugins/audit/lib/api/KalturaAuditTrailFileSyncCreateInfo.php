<?php
/**
 * @package plugins.audit
 * @subpackage api.objects
 */
class KalturaAuditTrailFileSyncCreateInfo extends KalturaAuditTrailInfo
{
	/**
	 * @var string
	 */
	public $version;

	/**
	 * @var int
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

	
	private static $map_between_objects = array
	(
		"version",
		"objectSubType",
		"dc",
		"original",
		"fileType",
	);
	
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
	/**
	 * @param kAuditTrailFileSyncCreateInfo $dbAuditTrail
	 * @param array $propsToSkip
	 * @return kAuditTrailInfo
	 */
	public function toObject($auditTrailInfo = null, $propsToSkip = array())
	{
		if(is_null($auditTrailInfo))
			$auditTrailInfo = new kAuditTrailFileSyncCreateInfo();
			
		return parent::toObject($auditTrailInfo, $propsToSkip);
	}
}
