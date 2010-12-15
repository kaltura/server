<?php
/**
 * @package api
 * @subpackage filters
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
			$this->userIdEqual = PuserKuserPeer::getKuserIdFromPuserId(kCurrentContext::$ks_partner_id, $this->userIdEqual);
		
		if(isset($this->userIdIn))
			$this->userIdIn = PuserKuserPeer::getKuserIdFromPuserIds(kCurrentContext::$ks_partner_id, explode(',', $this->userIdIn));
			
		return parent::toObject($auditTrailFilter, $propsToSkip);
	}
}
