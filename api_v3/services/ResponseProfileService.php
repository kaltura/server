<?php

/**
 * Manage response profiles
 *
 * @service responseProfile
 */
class ResponseProfileService extends KalturaBaseService
{
	public function initService($serviceId, $serviceName, $actionName)
	{
		parent::initService($serviceId, $serviceName, $actionName);
		$this->applyPartnerFilterForClass('ResponseProfile'); 	
	}
	
	/**
	 * Add new response profile
	 * 
	 * @action add
	 * @param KalturaResponseProfile $responseProfile
	 * @return KalturaResponseProfile
	 */
	function addAction(KalturaResponseProfile $responseProfile)
	{
		$dbResponseProfile = $responseProfile->toInsertableObject();
		$dbResponseProfile->setPartnerId($this->getPartnerId());
		$dbResponseProfile->save();
		
		$responseProfile = new KalturaResponseProfile();
		$responseProfile->fromObject($dbResponseProfile);
		return $responseProfile;
	}
	
	/**
	 * Get response profile by id
	 * 
	 * @action get
	 * @param int $id
	 * @return KalturaResponseProfile
	 * 
	 * @throws KalturaErrors::RESPONSE_PROFILE_ID_NOT_FOUND
	 */
	function getAction($id)
	{
		$dbResponseProfile = ResponseProfilePeer::retrieveByPK($id);
		if (!$dbResponseProfile)
			throw new KalturaAPIException(KalturaErrors::RESPONSE_PROFILE_ID_NOT_FOUND, $id);
			
		$responseProfile = new KalturaResponseProfile();
		$responseProfile->fromObject($dbResponseProfile);
		return $responseProfile;
	}
	
	/**
	 * Update response profile by id
	 * 
	 * @action update
	 * @param int $id
	 * @param KalturaResponseProfile $responseProfile
	 * @return KalturaResponseProfile
	 * 
	 * @throws KalturaErrors::RESPONSE_PROFILE_ID_NOT_FOUND
	 */
	function updateAction($id, KalturaResponseProfile $responseProfile)
	{
		$dbResponseProfile = ResponseProfilePeer::retrieveByPK($id);
		if (!$dbResponseProfile)
			throw new KalturaAPIException(KalturaErrors::RESPONSE_PROFILE_ID_NOT_FOUND, $id);
		
		$responseProfile->toUpdatableObject($dbResponseProfile);
		$dbResponseProfile->save();
		
		$responseProfile = new KalturaResponseProfile();
		$responseProfile->fromObject($dbResponseProfile);
		return $responseProfile;
	}
	
	/**
	 * Delete response profile by id
	 * 
	 * @action delete
	 * @param int $id
	 * 
	 * @throws KalturaErrors::RESPONSE_PROFILE_ID_NOT_FOUND
	 */
	function deleteAction($id)
	{
		$dbResponseProfile = ResponseProfilePeer::retrieveByPK($id);
		if (!$dbResponseProfile)
			throw new KalturaAPIException(KalturaErrors::RESPONSE_PROFILE_ID_NOT_FOUND, $id);

		$dbResponseProfile->setStatus(ResponseProfileStatus::DELETED);
		$dbResponseProfile->save();
	}
	
	/**
	 * List response profiles by filter and pager
	 * 
	 * @action list
	 * @param KalturaFilterPager $filter
	 * @param KalturaResponseProfileFilter $pager
	 * @return KalturaResponseProfileListResponse
	 */
	function listAction(KalturaResponseProfileFilter $filter = null, KalturaFilterPager $pager = null)
	{
		if (!$filter)
			$filter = new KalturaResponseProfileFilter();

		if (!$pager)
			$pager = new KalturaFilterPager();
			
		$responseProfileFilter = new responseProfileFilter();
		$filter->toObject($responseProfileFilter);

		$c = new Criteria();
		$responseProfileFilter->attachToCriteria($c);
		
		$totalCount = ResponseProfilePeer::doCount($c);
		
		$pager->attachToCriteria($c);
		$dbList = ResponseProfilePeer::doSelect($c);
		
		$list = KalturaResponseProfileArray::fromDbArray($dbList);
		$response = new KalturaResponseProfileListResponse();
		$response->objects = $list;
		$response->totalCount = $totalCount;
		return $response;    
	}
}