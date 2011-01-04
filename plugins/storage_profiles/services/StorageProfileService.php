<?php
/**
 * Storage Profiles service
 *
 * @service storageProfile
 */
class StorageProfileService extends KalturaBaseService
{
	public function initService($partnerId, $puserId, $ksStr, $serviceName, $action)
	{
		parent::initService($partnerId, $puserId, $ksStr, $serviceName, $action);

		// since plugin might be using KS impersonation, we need to validate the requesting
		// partnerId from the KS and not with the $_POST one
		if(!StorageProfilePlugin::isAllowedPartner($partnerId))
			throw new KalturaAPIException(KalturaErrors::SERVICE_FORBIDDEN);
	}
	
	/**
	 * @action listByPartner
	 * @param KalturaPartnerFilter $filter
	 * @param KalturaFilterPager $pager
	 * @return KalturaStorageProfileListResponse
	 */
	public function listByPartnerAction(KalturaPartnerFilter $filter = null, KalturaFilterPager $pager = null)
	{
		$c = new Criteria();
		
		if (!is_null($filter))
		{
			
			$partnerFilter = new partnerFilter();
			$filter->toObject($partnerFilter);
			$partnerFilter->set('_gt_id', 0);
			
			$partnerCriteria = new Criteria();
			$partnerFilter->attachToCriteria($partnerCriteria);
			$partnerCriteria->setLimit(1000);
			$partnerCriteria->clearSelectColumns();
			$partnerCriteria->addSelectColumn(PartnerPeer::ID);
			$stmt = PartnerPeer::doSelectStmt($partnerCriteria);
			
			if($stmt->rowCount() < 1000) // otherwise, it's probably all partners
			{
				$partnerIds = $stmt->fetchAll(PDO::FETCH_COLUMN);
				$c->add(StorageProfilePeer::PARTNER_ID, $partnerIds, Criteria::IN);
			}
		}
			
		if (is_null($pager))
			$pager = new KalturaFilterPager();
		
		$totalCount = StorageProfilePeer::doCount($c);
		$pager->attachToCriteria($c);
		$list = StorageProfilePeer::doSelect($c);
		$newList = KalturaStorageProfileArray::fromStorageProfileArray($list);
		
		$response = new KalturaStorageProfileListResponse();
		$response->totalCount = $totalCount;
		$response->objects = $newList;
		return $response;
	}
	
	/**
	 * @action updateStatus
	 * @param int $storageId
	 * @param KalturaStorageProfileStatus $status
	 */
	public function updateStatusAction($storageId, $status)
	{
		$dbStorage = StorageProfilePeer::retrieveByPK($storageId);
		if (!$dbStorage)
			throw new KalturaAPIException(KalturaErrors::INVALID_OBJECT_ID, $storageId);
			
		$dbStorage->setStatus($status);
		$dbStorage->save();
	}	
	
	/**
	 * Get storage profile by id
	 * 
	 * @action get
	 * @param int $storageProfileId
	 * @return KalturaStorageProfile
	 */
	function getAction($storageProfileId)
	{
		$dbStorageProfile = StorageProfilePeer::retrieveByPK($storageProfileId);
		if (!$dbStorageProfile)
			return null;
			
		$storageProfile = new KalturaStorageProfile();
		$storageProfile->fromObject($dbStorageProfile);
		return $storageProfile;
	}
	
	/**
	 * Update storage profile by id 
	 * 
	 * @action update
	 * @param int $storageProfileId
	 * @param KalturaStorageProfile $storageProfile
	 * @return KalturaStorageProfile
	 */
	function updateAction($storageProfileId, KalturaStorageProfile $storageProfile)
	{
		$dbStorageProfile = StorageProfilePeer::retrieveByPK($storageProfileId);
		if (!$dbStorageProfile)
			throw new KalturaAPIException(KalturaErrors::INVALID_OBJECT_ID, $storageProfileId);
			
		$dbStorageProfile = $storageProfile->toUpdatableObject($dbStorageProfile);
		$dbStorageProfile->save();
		
		$storageProfile->fromObject($dbStorageProfile);
		return $storageProfile;
	}


	/**
	 * Adds a storage profile to the Kaltura DB.
	 *
	 * @action add
	 * @param KalturaStorageProfile $storageProfile 
	 * @return KalturaStorageProfile
	 */
	function addAction(KalturaStorageProfile $storageProfile)
	{
		if(!$storageProfile->status)
			$storageProfile->status = KalturaStorageProfileStatus::DISABLED;
			
		$dbStorageProfile = $storageProfile->toInsertableObject();
		$dbStorageProfile->setPartnerId($this->impersonatedPartnerId);
		$dbStorageProfile->save();
		
		$storageProfile->fromObject($dbStorageProfile);
		return $storageProfile;
	}
}
