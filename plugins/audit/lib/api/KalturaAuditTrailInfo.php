<?php
/**
 * @package plugins.audit
 * @subpackage api.objects
 * @abstract
 */
class KalturaAuditTrailInfo extends KalturaObject 
{
	/**
	 * @param kAuditTrailInfo $dbAuditTrail
	 * @param array $propsToSkip
	 * @return kAuditTrailInfo
	 */
	public function toObject($auditTrailInfo = null, $propsToSkip = array())
	{
		if(is_null($auditTrailInfo))
			$auditTrailInfo = new kAuditTrailInfo();
			
		return parent::toObject($auditTrailInfo, $propsToSkip);
	}
}
