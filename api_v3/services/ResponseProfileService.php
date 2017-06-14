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
		
		//Don;t apply partner filter if action is list to avoid returning default partner 0 response profiles on every call
		if($actionName !== "list")
			$this->applyPartnerFilterForClass('ResponseProfile'); 	
	}
	
	/* (non-PHPdoc)
	 * @see KalturaBaseService::partnerGroup()
	 */
	protected function partnerGroup($peer = null)
	{
		
		switch ($this->actionName)
		{
			case 'get':
				return $this->partnerGroup . ',0';
			//When requesting response profiles allow default once in case requesting partner is internal
			case 'list':
				if(kCurrentContext::$ks_partner_id <= 0)
					return $this->partnerGroup . ',0';
		}
			
		return $this->partnerGroup;
	}
	
	/**
	 * Add new response profile
	 * 
	 * @action add
	 * @param KalturaResponseProfile $addResponseProfile
	 * @return KalturaResponseProfile
	 */
	function addAction(KalturaResponseProfile $addResponseProfile)
	{
		$dbResponseProfile = $addResponseProfile->toInsertableObject();
		/* @var $dbResponseProfile ResponseProfile */
		$dbResponseProfile->setPartnerId(kCurrentContext::getCurrentPartnerId(true));
		$dbResponseProfile->setStatus(ResponseProfileStatus::ENABLED);
		$dbResponseProfile->save();
		
		$addResponseProfile = new KalturaResponseProfile();
		$addResponseProfile->fromObject($dbResponseProfile, $this->getResponseProfile());
		return $addResponseProfile;
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
		$responseProfile->fromObject($dbResponseProfile, $this->getResponseProfile());
		return $responseProfile;
	}
	
	/**
	 * Update response profile by id
	 * 
	 * @action update
	 * @param int $id
	 * @param KalturaResponseProfile $updateResponseProfile
	 * @return KalturaResponseProfile
	 * 
	 * @throws KalturaErrors::RESPONSE_PROFILE_ID_NOT_FOUND
	 */
	function updateAction($id, KalturaResponseProfile $updateResponseProfile)
	{
		$dbResponseProfile = ResponseProfilePeer::retrieveByPK($id);
		if (!$dbResponseProfile)
			throw new KalturaAPIException(KalturaErrors::RESPONSE_PROFILE_ID_NOT_FOUND, $id);
		
		$updateResponseProfile->toUpdatableObject($dbResponseProfile);
		$dbResponseProfile->save();
		
		$updateResponseProfile = new KalturaResponseProfile();
		$updateResponseProfile->fromObject($dbResponseProfile, $this->getResponseProfile());
		return $updateResponseProfile;
	}

	/**
	 * Update response profile status by id
	 * 
	 * @action updateStatus
	 * @param int $id
	 * @param KalturaResponseProfileStatus $status
	 * @return KalturaResponseProfile
	 * 
	 * @throws KalturaErrors::RESPONSE_PROFILE_ID_NOT_FOUND
	 */
	function updateStatusAction($id, $status)
	{
		$dbResponseProfile = ResponseProfilePeer::retrieveByPK($id);
		if (!$dbResponseProfile)
			throw new KalturaAPIException(KalturaErrors::RESPONSE_PROFILE_ID_NOT_FOUND, $id);

		if($status == KalturaResponseProfileStatus::ENABLED)
		{
			//Check uniqueness of new object's system name
			$systemNameProfile = ResponseProfilePeer::retrieveBySystemName($dbResponseProfile->getSystemName(), $id);
			if ($systemNameProfile)
				throw new KalturaAPIException(KalturaErrors::RESPONSE_PROFILE_DUPLICATE_SYSTEM_NAME, $dbResponseProfile->getSystemName());
		}	
		
		$dbResponseProfile->setStatus($status);
		$dbResponseProfile->save();
	
		$responseProfile = new KalturaResponseProfile();
		$responseProfile->fromObject($dbResponseProfile, $this->getResponseProfile());
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
		
		//Add partner 0 to filter only in case systemNmae or Id are provided in the filter to avoid returning it by default
		if(isset($filter->systemNameEqual) || isset($filter->idEqual)) {
			$this->partnerGroup .= ",0";
		}
		$this->applyPartnerFilterForClass('ResponseProfile');

		if (!$pager)
			$pager = new KalturaFilterPager();
			
		$responseProfileFilter = new ResponseProfileFilter();
		$filter->toObject($responseProfileFilter);

		$c = new Criteria();
		$responseProfileFilter->attachToCriteria($c);
		
		$totalCount = ResponseProfilePeer::doCount($c);
		
		$pager->attachToCriteria($c);
		$dbList = ResponseProfilePeer::doSelect($c);
		
		$list = KalturaResponseProfileArray::fromDbArray($dbList, $this->getResponseProfile());
		$response = new KalturaResponseProfileListResponse();
		$response->objects = $list;
		$response->totalCount = $totalCount;
		return $response;
	}
	
	/**
	 * Recalculate response profile cached objects
	 * 
	 * @action recalculate
	 * @param KalturaResponseProfileCacheRecalculateOptions $options
	 * @return KalturaResponseProfileCacheRecalculateResults
	 */
	function recalculateAction(KalturaResponseProfileCacheRecalculateOptions $options)
	{
		return KalturaResponseProfileCacher::recalculateCacheBySessionType($options);
	}
	
	/**
	 * Clone an existing response profile
	 * 
	 * @action clone
	 * @param int $id
	 * @param KalturaResponseProfile $profile
	 * @throws KalturaErrors::RESPONSE_PROFILE_ID_NOT_FOUND
	 * @throws KalturaErrors::RESPONSE_PROFILE_DUPLICATE_SYSTEM_NAME
	 * @return KalturaResponseProfile
	 */
	function cloneAction ($id, KalturaResponseProfile $profile)
	{
		$origResponseProfileDbObject = ResponseProfilePeer::retrieveByPK($id);
		if (!$origResponseProfileDbObject)
			throw new KalturaAPIException(KalturaErrors::RESPONSE_PROFILE_ID_NOT_FOUND, $id);
			
		$newResponseProfileDbObject = $origResponseProfileDbObject->copy();
		
		if ($profile)
			$newResponseProfileDbObject = $profile->toInsertableObject($newResponseProfileDbObject);
				
		$newResponseProfileDbObject->save();
		
		$newResponseProfile = new KalturaResponseProfile();
		$newResponseProfile->fromObject($newResponseProfileDbObject, $this->getResponseProfile());
		return $newResponseProfile;
	}
}