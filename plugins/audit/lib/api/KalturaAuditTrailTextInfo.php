<?php
class KalturaAuditTrailTextInfo extends KalturaAuditTrailInfo
{
	/**
	 * @var string
	 */
	public $info;
	
	private static $map_between_objects = array
	(
		"info",
	);
	
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
	/**
	 * @param kAuditTrailTextInfo $dbAuditTrail
	 * @param array $propsToSkip
	 * @return kAuditTrailInfo
	 */
	public function toObject($auditTrailInfo = null, $propsToSkip = array())
	{
		if(is_null($auditTrailInfo))
			$auditTrailInfo = new kAuditTrailTextInfo();
			
		return parent::toObject($auditTrailInfo, $propsToSkip);
	}
}
