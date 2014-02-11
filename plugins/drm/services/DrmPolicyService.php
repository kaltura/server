<?php
/**
 * 
 * @service drmPolicy
 * @package plugins.drm
 * @subpackage api.services
 */
class DrmPolicyService extends KalturaBaseService
{	
	public function initService($serviceId, $serviceName, $actionName)
	{
		parent::initService($serviceId, $serviceName, $actionName);
		if (!DrmPlugin::isAllowedPartner($this->getPartnerId()))
			throw new KalturaAPIException(KalturaErrors::SERVICE_FORBIDDEN, $this->serviceName.'->'.$this->actionName);
			
		$this->applyPartnerFilterForClass('DrmPolicy');
	}
	
	/**
	 * Allows you to add a new DrmPolicy object
	 * 
	 * @action add
	 * @param KalturaDrmPolicy $drmPolicy
	 * @return KalturaDrmPolicy
	 * 
	 * @throws KalturaErrors::PROPERTY_VALIDATION_CANNOT_BE_NULL
	 */
	public function addAction(KalturaDrmPolicy $drmPolicy)
	{
		// check for required parameters
		$drmPolicy->validatePropertyNotNull('name');
		$drmPolicy->validatePropertyNotNull('status');
		$drmPolicy->validatePropertyNotNull('provider');
		$drmPolicy->validatePropertyNotNull('systemName');
		$drmPolicy->validatePropertyNotNull('scenario');
		$drmPolicy->validatePropertyNotNull('partnerId');
		$drmPolicy->validatePropertyNotNull('profileId');
		
		// validate values
		$drmPolicy->validatePolicy();
						
		if (!PartnerPeer::retrieveByPK($drmPolicy->partnerId)) {
			throw new KalturaAPIException(KalturaErrors::INVALID_PARTNER_ID, $drmPolicy->partnerId);
		}
		
		if (!DrmPlugin::isAllowedPartner($drmPolicy->partnerId))
		{
			throw new KalturaAPIException(KalturaErrors::PLUGIN_NOT_AVAILABLE_FOR_PARTNER, DrmPlugin::getPluginName(), $drmPolicy->partnerId);
		}

		if(DrmPolicyPeer::retrieveBySystemName($drmPolicy->systemName))
		{
			throw new KalturaAPIException(DrmErrors::DRM_POLICY_DUPLICATE_SYSTEM_NAME, $drmPolicy->systemName);
		}
				
		// save in database
		$dbDrmPolicy = $drmPolicy->toInsertableObject();
		$dbDrmPolicy->save();
		
		// return the saved object
		$drmPolicy = KalturaDrmPolicy::getInstanceByType($dbDrmPolicy->getProvider());
		$drmPolicy->fromObject($dbDrmPolicy);
		return $drmPolicy;
		
	}
	
	/**
	 * Retrieve a KalturaDrmPolicy object by ID
	 * 
	 * @action get
	 * @param int $drmPolicyId 
	 * @return KalturaDrmPolicy
	 * 
	 * @throws KalturaErrors::INVALID_OBJECT_ID
	 */		
	public function getAction($drmPolicyId)
	{
		$dbDrmPolicy = DrmPolicyPeer::retrieveByPK($drmPolicyId);
		
		if (!$dbDrmPolicy) {
			throw new KalturaAPIException(KalturaErrors::INVALID_OBJECT_ID, $drmPolicyId);
		}
			
		$drmPolicy = KalturaDrmPolicy::getInstanceByType($dbDrmPolicy->getProvider());
		$drmPolicy->fromObject($dbDrmPolicy);
		
		return $drmPolicy;
	}
	

	/**
	 * Update an existing KalturaDrmPolicy object
	 * 
	 * @action update
	 * @param int $drmPolicyId
	 * @param KalturaDrmPolicy $drmPolicy
	 * @return KalturaDrmPolicy
	 *
	 * @throws KalturaErrors::INVALID_OBJECT_ID
	 */	
	public function updateAction($drmPolicyId, KalturaDrmPolicy $drmPolicy)
	{
		$dbDrmPolicy = DrmPolicyPeer::retrieveByPK($drmPolicyId);
		
		if (!$dbDrmPolicy) {
			throw new KalturaAPIException(KalturaErrors::INVALID_OBJECT_ID, $drmPolicyId);
		}
		
		$drmPolicy->validatePolicy();
						
		$dbDrmPolicy = $drmPolicy->toUpdatableObject($dbDrmPolicy);
		$dbDrmPolicy->save();
	
		$drmPolicy = KalturaDrmPolicy::getInstanceByType($dbDrmPolicy->getProvider());
		$drmPolicy->fromObject($dbDrmPolicy);
		
		return $drmPolicy;
	}

	/**
	 * Mark the KalturaDrmPolicy object as deleted
	 * 
	 * @action delete
	 * @param int $drmPolicyId 
	 * @return KalturaDrmPolicy
	 *
	 * @throws KalturaErrors::INVALID_OBJECT_ID
	 */		
	public function deleteAction($drmPolicyId)
	{
		$dbDrmPolicy = DrmPolicyPeer::retrieveByPK($drmPolicyId);
		
		if (!$dbDrmPolicy) {
			throw new KalturaAPIException(KalturaErrors::INVALID_OBJECT_ID, $drmPolicyId);
		}

		$dbDrmPolicy->setStatus(DrmPolicyStatus::DELETED);
		$dbDrmPolicy->save();
			
		$drmPolicy = KalturaDrmPolicy::getInstanceByType($dbDrmPolicy->getProvider());
		$drmPolicy->fromObject($dbDrmPolicy);
		
		return $drmPolicy;
	}
	
	/**
	 * List KalturaDrmPolicy objects
	 * 
	 * @action list
	 * @param KalturaDrmPolicyFilter $filter
	 * @param KalturaFilterPager $pager
	 * @return KalturaDrmPolicyListResponse
	 */
	public function listAction(KalturaDrmPolicyFilter  $filter = null, KalturaFilterPager $pager = null)
	{
		if (!$filter)
			$filter = new KalturaDrmPolicyFilter();
			
		$drmPolicyFilter = $filter->toObject();

		$c = new Criteria();
		$drmPolicyFilter->attachToCriteria($c);
		$count = DrmPolicyPeer::doCount($c);		
		if (! $pager)
			$pager = new KalturaFilterPager ();
		$pager->attachToCriteria ( $c );
		$list = DrmPolicyPeer::doSelect($c);
		
		$response = new KalturaDrmPolicyListResponse();
		$response->objects = KalturaDrmPolicyArray::fromDbArray($list);
		$response->totalCount = $count;
		
		return $response;
	}

}
