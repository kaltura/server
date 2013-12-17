<?php
/**
 * Audit Trail service
 *
 * @service auditTrail
 * @package plugins.audit
 * @subpackage api.services
 */
class AuditTrailService extends KalturaBaseService
{
	public function initService($serviceId, $serviceName, $actionName)
	{
		parent::initService($serviceId, $serviceName, $actionName);

		$this->applyPartnerFilterForClass('AuditTrail');
		$this->applyPartnerFilterForClass('AuditTrailData');
		$this->applyPartnerFilterForClass('AuditTrailConfig');
		
		if(!AuditPlugin::isAllowedPartner($this->getPartnerId()))
			throw new KalturaAPIException(KalturaErrors::FEATURE_FORBIDDEN, AuditPlugin::PLUGIN_NAME);
	}
	
	/**
	 * Allows you to add an audit trail object and audit trail content associated with Kaltura object
	 * 
	 * @action add
	 * @param KalturaAuditTrail $auditTrail
	 * @return KalturaAuditTrail
	 * @throws AuditTrailErrors::AUDIT_TRAIL_DISABLED
	 */
	function addAction(KalturaAuditTrail $auditTrail)
	{
		$auditTrail->validatePropertyNotNull("auditObjectType");
		$auditTrail->validatePropertyNotNull("objectId");
		$auditTrail->validatePropertyNotNull("action");
		$auditTrail->validatePropertyMaxLength("description", 1000);
		
		$dbAuditTrail = $auditTrail->toInsertableObject();
		$dbAuditTrail->setPartnerId($this->getPartnerId());
		$dbAuditTrail->setStatus(AuditTrail::AUDIT_TRAIL_STATUS_READY);
		$dbAuditTrail->setContext(KalturaAuditTrailContext::CLIENT);
		
		$enabled = kAuditTrailManager::traceEnabled($this->getPartnerId(), $dbAuditTrail);
		if(!$enabled)
			throw new KalturaAPIException(AuditTrailErrors::AUDIT_TRAIL_DISABLED, $this->getPartnerId(), $dbAuditTrail->getObjectType(), $dbAuditTrail->getAction());
			
		$created = $dbAuditTrail->save();
		if(!$created)
			return null;
		
		$auditTrail = new KalturaAuditTrail();
		$auditTrail->fromObject($dbAuditTrail);
		
		return $auditTrail;
	}
	
	/**
	 * Retrieve an audit trail object by id
	 * 
	 * @action get
	 * @param int $id 
	 * @return KalturaAuditTrail
	 * @throws KalturaErrors::INVALID_OBJECT_ID
	 */		
	function getAction($id)
	{
		$dbAuditTrail = AuditTrailPeer::retrieveByPK( $id );
		
		if(!$dbAuditTrail)
			throw new KalturaAPIException(KalturaErrors::INVALID_OBJECT_ID, $id);
			
		$auditTrail = new KalturaAuditTrail();
		$auditTrail->fromObject($dbAuditTrail);
		
		return $auditTrail;
	}

		/**
	 * List audit trail objects by filter and pager
	 * 
	 * @action list
	 * @param KalturaAuditTrailFilter $filter
	 * @param KalturaFilterPager $pager
	 * @return KalturaAuditTrailListResponse
	 */
	function listAction(KalturaAuditTrailFilter $filter = null, KalturaFilterPager $pager = null)
	{
		if (!$filter)
			$filter = new KalturaAuditTrailFilter;
			
		$auditTrailFilter = $filter->toObject();
		
		$c = new Criteria();
		$auditTrailFilter->attachToCriteria($c);
		$count = AuditTrailPeer::doCount($c);
		
		if (!$pager)
			$pager = new KalturaFilterPager();
			
		$pager->attachToCriteria($c);
		$list = AuditTrailPeer::doSelect($c);
		
		$response = new KalturaAuditTrailListResponse();
		$response->objects = KalturaAuditTrailArray::fromDbArray($list);
		$response->totalCount = $count;
		
		return $response;
	}
}
