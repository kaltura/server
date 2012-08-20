<?php
/**
 * @package plugins.audit
 * @subpackage api.objects
 */
class KalturaAuditTrailChangeItem extends KalturaObject
{
	/**
	 * @var string
	 */
	public $descriptor;
	
	/**
	 * @var string
	 */
	public $oldValue;
	
	/**
	 * @var string
	 */
	public $newValue;

	
	private static $map_between_objects = array
	(
		"descriptor",
		"oldValue",
		"newValue",
	);
	
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
	/**
	 * @param kAuditTrailChangeItem $dbAuditTrail
	 * @param array $propsToSkip
	 * @return kAuditTrailInfo
	 */
	public function toObject($auditTrailInfo = null, $propsToSkip = array())
	{
		if(is_null($auditTrailInfo))
			$auditTrailInfo = new kAuditTrailChangeItem();
			
		return parent::toObject($auditTrailInfo, $propsToSkip);
	}
}
