<?php
/**
 * Virus scan profile service
 *
 * @service virusScanProfile
 */
class VirusScanProfileService extends KalturaBaseService
{
	public function initService($partnerId, $puserId, $ksStr, $serviceName, $action)
	{
		parent::initService($partnerId, $puserId, $ksStr, $serviceName, $action);

		myPartnerUtils::addPartnerToCriteria(new VirusScanProfilePeer(), $this->getPartnerId(), $this->private_partner_data, $this->partnerGroup());
		myPartnerUtils::addPartnerToCriteria(new flavorAssetPeer(), $this->getPartnerId(), $this->private_partner_data, $this->partnerGroup());
		
		if(!VirusScanPlugin::isAllowedPartner(kCurrentContext::$ks_partner_id))
			throw new KalturaAPIException(KalturaErrors::SERVICE_FORBIDDEN);
	}
	
	/**
	 * List virus scan profile objects by filter and pager
	 * 
	 * @action list
	 * @param KalturaVirusScanProfileFilter $filter
	 * @param KalturaFilterPager $pager
	 * @return KalturaVirusScanProfileListResponse
	 */
	function listAction(KalturaVirusScanProfileFilter  $filter = null, KalturaFilterPager $pager = null)
	{
		if (!$filter)
			$filter = new KalturaVirusScanProfileFilter;
			
		$virusScanProfileFilter = $filter->toObject();
		
		$c = new Criteria();
		$virusScanProfileFilter->attachToCriteria($c);
		$count = VirusScanProfilePeer::doCount($c);
		
		if ($pager)
			$pager->attachToCriteria($c);
		$list = VirusScanProfilePeer::doSelect($c);
		
		$response = new KalturaVirusScanProfileListResponse();
		$response->objects = KalturaVirusScanProfileArray::fromDbArray($list);
		$response->totalCount = $count;
		
		return $response;
	}
	
	/**
	 * Allows you to add an virus scan profile object and virus scan profile content associated with Kaltura object
	 * 
	 * @action add
	 * @param KalturaVirusScanProfile $virusScanProfile
	 * @return KalturaVirusScanProfile
	 */
	function addAction(KalturaVirusScanProfile $virusScanProfile)
	{
		$virusScanProfile->validatePropertyNotNull("engineType");
		$virusScanProfile->validatePropertyNotNull("actionIfInfected");
		$virusScanProfile->validatePropertyMaxLength("name", 30);
		
		if(!$virusScanProfile->name)
			$virusScanProfile->name = time();
			
		if(!$virusScanProfile->status)
			$virusScanProfile->status = KalturaVirusScanProfileStatus::DISABLED;
			
		$dbVirusScanProfile = $virusScanProfile->toInsertableObject();
		$dbVirusScanProfile->setPartnerId($this->getPartnerId());
		$dbVirusScanProfile->save();
		
		$virusScanProfile = new KalturaVirusScanProfile();
		$virusScanProfile->fromObject($dbVirusScanProfile);
		
		return $virusScanProfile;
	}
	
	/**
	 * Retrieve an virus scan profile object by id
	 * 
	 * @action get
	 * @param int $virusScanProfileId 
	 * @return KalturaVirusScanProfile
	 * @throws KalturaErrors::INVALID_OBJECT_ID
	 */		
	function getAction($virusScanProfileId)
	{
		$dbVirusScanProfile = VirusScanProfilePeer::retrieveByPK( $virusScanProfileId );
		
		if(!$dbVirusScanProfile)
			throw new KalturaAPIException(KalturaErrors::INVALID_OBJECT_ID, $virusScanProfileId);
			
		$virusScanProfile = new KalturaVirusScanProfile();
		$virusScanProfile->fromObject($dbVirusScanProfile);
		
		return $virusScanProfile;
	}


	/**
	 * Update exisitng virus scan profile, it is possible to update the virus scan profile id too
	 * 
	 * @action update
	 * @param int $virusScanProfileId
	 * @param KalturaVirusScanProfile $virusScanProfile
	 * @return KalturaVirusScanProfile
	 *
	 * @throws KalturaErrors::INVALID_OBJECT_ID
	 */	
	function updateAction($virusScanProfileId, KalturaVirusScanProfile $virusScanProfile)
	{
		$dbVirusScanProfile = VirusScanProfilePeer::retrieveByPK($virusScanProfileId);
	
		if (!$dbVirusScanProfile)
			throw new KalturaAPIException(KalturaErrors::INVALID_OBJECT_ID, $virusScanProfileId);
		
		$dbVirusScanProfile = $virusScanProfile->toUpdatableObject($dbVirusScanProfile);
		$dbVirusScanProfile->save();
	
		$virusScanProfile->fromObject($dbVirusScanProfile);
		
		return $virusScanProfile;
	}

	/**
	 * Mark the virus scan profile as deleted
	 * 
	 * @action delete
	 * @param int $virusScanProfileId 
	 * @return KalturaVirusScanProfile
	 *
	 * @throws KalturaErrors::INVALID_OBJECT_ID
	 */		
	function deleteAction($virusScanProfileId)
	{
		$dbVirusScanProfile = VirusScanProfilePeer::retrieveByPK($virusScanProfileId);
	
		if (!$dbVirusScanProfile)
			throw new KalturaAPIException(KalturaErrors::INVALID_OBJECT_ID, $virusScanProfileId);
		
		$dbVirusScanProfile->setStatus(KalturaVirusScanProfileStatus::DISABLED);
		$dbVirusScanProfile->save();
			
		$virusScanProfile = new KalturaVirusScanProfile();
		$virusScanProfile->fromObject($dbVirusScanProfile);
		
		return $virusScanProfile;
	}

	/**
	 * Scan flavor asset according to virus scan profile
	 * 
	 * @action scan
	 * @param int $virusScanProfileId
	 * @param string $flavorAssetId 
	 * @return int job id
	 *
	 * @throws KalturaErrors::INVALID_OBJECT_ID
	 * @throws KalturaErrors::INVALID_FLAVOR_ASSET_ID
	 * @throws KalturaErrors::INVALID_FILE_SYNC_ID
	 */		
	function scanAction($virusScanProfileId, $flavorAssetId)
	{
		$dbVirusScanProfile = VirusScanProfilePeer::retrieveByPK($virusScanProfileId);
		if (!$dbVirusScanProfile)
			throw new KalturaAPIException(KalturaErrors::INVALID_OBJECT_ID, $virusScanProfileId);
	
		$dbFlavorAsset = flavorAssetPeer::retrieveById($flavorAssetId);
		if (!$dbFlavorAsset)
			throw new KalturaAPIException(KalturaErrors::INVALID_FLAVOR_ASSET_ID, $flavorAssetId);
		
		$syncKey = $dbFlavorAsset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
		$srcFilePath = kFileSyncUtils::getLocalFilePathForKey($syncKey);
		if(!$srcFilePath)
			throw new KalturaAPIException(KalturaErrors::INVALID_FILE_SYNC_ID, $syncKey);
			
		$job = kVirusScanJobsManager::addVirusScanJob(null, $dbFlavorAsset->getPartnerId(), $dbFlavorAsset->getEntryId(), $dbFlavorAsset->getId(), $srcFilePath, $dbVirusScanProfile->getEngineType());
		return $job->getId();
	}
}
