<?php
/**
 * @package api
 * @subpackage filters
 */
class KalturaAuditTrailFilter extends KalturaAuditTrailBaseFilter
{
	/**
	 * @param AuditTrailFilter $auditTrailFilter
	 * @param array $propsToSkip
	 * @return AuditTrailFilter
	 */
	public function toObject($auditTrailFilter = null, $propsToSkip = array())
	{
		if(!$auditTrailFilter)
			$auditTrailFilter = new AuditTrailFilter();
			
		if(isset($this->userIdEqual))
			$this->userIdEqual = PuserKuserPeer::getKuserIdFromPuserId(kCurrentContext::$partner_id, $this->userIdEqual);
		
		if(isset($this->userIdIn))
			$this->userIdIn = PuserKuserPeer::getKuserIdFromPuserIds(kCurrentContext::$partner_id, explode(',', $this->userIdIn));
			
		return parent::toObject($auditTrailFilter, $propsToSkip);
	}
}
