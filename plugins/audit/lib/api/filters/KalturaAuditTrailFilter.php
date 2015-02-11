<?php
/**
 * @package plugins.audit
 * @subpackage api.filters
 */
class KalturaAuditTrailFilter extends KalturaAuditTrailBaseFilter
{
	static private $map_between_objects = array
	(
		"auditObjectTypeEqual" => "_eq_object_type",
		"auditObjectTypeIn" => "_in_object_type",
	);

	static private $order_by_map = array
	(
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

	public function getOrderByMap()
	{
		return array_merge(parent::getOrderByMap(), self::$order_by_map);
	}

	/* (non-PHPdoc)
	 * @see KalturaFilter::getCoreFilter()
	 */
	protected function getCoreFilter()
	{
		return new AuditTrailFilter();
	}
	
	/**
	 * @param AuditTrailFilter $auditTrailFilter
	 * @param array $propsToSkip
	 * @return AuditTrailFilter
	 */
	public function toObject($auditTrailFilter = null, $propsToSkip = array())
	{
		if(isset($this->userIdEqual))
		{
			$kuser = kuserPeer::getKuserByPartnerAndUid(kCurrentContext::$ks_partner_id, $this->userIdEqual, true);
			if($kuser)
				$this->userIdEqual = $kuser->getId();
		}
		
		if(isset($this->userIdIn))
		{
			$kusers = kuserPeer::getKuserByPartnerAndUids(kCurrentContext::$ks_partner_id, $this->userIdIn);
			$kuserIds = array();
			foreach($kusers as $kuser)
				$kuserIds[] = $kuser->getId();
				
			$this->userIdIn = implode(',', $kuserIds);
		}
			
		return parent::toObject($auditTrailFilter, $propsToSkip);
	}
}
