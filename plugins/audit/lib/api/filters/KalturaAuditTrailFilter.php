<?php
/**
 * @package plugins.audit
 * @subpackage api.filters
 */
class KalturaAuditTrailFilter extends KalturaAuditTrailBaseFilter
{
	private $map_between_objects = array
	(
		"auditObjectTypeEqual" => "_eq_object_type",
		"auditObjectTypeIn" => "_in_object_type",
	);

	private $order_by_map = array
	(
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), $this->map_between_objects);
	}

	public function getOrderByMap()
	{
		return array_merge(parent::getOrderByMap(), $this->order_by_map);
	}
	
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
		{
			$kuser = KuserPeer::getKuserByPartnerAndUid(kCurrentContext::$ks_partner_id, $this->userIdEqual, true);
			if($kuser)
				$this->userIdEqual = $kuser->getId();
		}
		
		if(isset($this->userIdIn))
		{
			$kusers = KuserPeer::getKuserByPartnerAndUids(kCurrentContext::$ks_partner_id, $this->userIdIn);
			$kuserIds = array();
			foreach($kusers as $kuser)
				$kuserIds[] = $kuser->getId();
				
			$this->userIdIn = implode(',', $kuserIds);
		}
			
		return parent::toObject($auditTrailFilter, $propsToSkip);
	}
}
