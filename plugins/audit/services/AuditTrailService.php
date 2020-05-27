<?php
/**
 * The Audit Trail service allows you to keep track of changes made to various Kaltura objects. 
 * This service is disabled by default.
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
		$auditTrail->fromObject($dbAuditTrail, $this->getResponseProfile());
		
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
		$auditTrail->fromObject($dbAuditTrail, $this->getResponseProfile());
		
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
			
		if (!$pager)
			$pager = new KalturaFilterPager();

		$response =  $filter->getListResponse($pager, $this->getResponseProfile());

		$params = infraRequestUtils::getRequestParams();
		$clientsTags = isset($params[infraRequestUtils::CLIENT_TAG]) ? $params[infraRequestUtils::CLIENT_TAG] : null;
		if ($clientsTags && $clientsTags === 'Kaltura-admin')
		{
			$response =  self::handleResponse($response);
		}
		return $response;
	}

	public function handleResponse($response)
	{
		$descriptors = array('vendor_credit');
		foreach ($response->objects as $auditTrail)
		{
			if (isset($auditTrail->data) && isset($auditTrail->data->changedItems))
			{
				foreach ($auditTrail->data->changedItems as $changedItem)
				{
					if (in_array($changedItem->descriptor, $descriptors))
					{
						$oldValue = unserialize($changedItem->oldValue);
						$newValue = unserialize($changedItem->newValue);
						$resultOldValue = $oldValue->getObjectAsArray();
						$resultNewValue = $newValue->getObjectAsArray();
						$changedItem->oldValue =  str_replace ( ',' , ",\n" , json_encode($resultOldValue));
						$changedItem->newValue = str_replace ( ',' , ",\n" , json_encode($resultNewValue));
					}
				}
			}
		}
		return $response;
	}
}
