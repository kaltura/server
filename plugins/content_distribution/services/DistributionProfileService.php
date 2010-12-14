<?php
/**
 * Distribution Profile service
 *
 * @service distributionProfile
 */
class DistributionProfileService extends KalturaBaseService
{
	public function initService($partnerId, $puserId, $ksStr, $serviceName, $action)
	{
		parent::initService($partnerId, $puserId, $ksStr, $serviceName, $action);

		myPartnerUtils::addPartnerToCriteria(new DistributionProfilePeer(), $this->getPartnerId(), $this->private_partner_data, $this->partnerGroup());
		
		if(!ContentDistributionPlugin::isAllowedPartner($this->getPartnerId()))
			throw new KalturaAPIException(KalturaErrors::SERVICE_FORBIDDEN);
	}
	
	/**
	 * Add new Distribution Profile
	 * 
	 * @action add
	 * @param KalturaDistributionProfile $distributionProfile
	 * @return KalturaDistributionProfile
	 * @throws ContentDistributionErrors::DISTRIBUTION_PROVIDER_NOT_FOUND
	 */
	function addAction(KalturaDistributionProfile $distributionProfile)
	{
		$distributionProfile->validatePropertyMinLength("name", 1);
		$distributionProfile->validatePropertyNotNull("providerType");
					
		if(is_null($distributionProfile->status))
			$distributionProfile->status = KalturaDistributionProfileStatus::DISABLED;
		
		$dbDistributionProfile = DistributionProfilePeer::createDistributionProfile($distributionProfile->providerType);
		if(!$dbDistributionProfile)
			throw new KalturaAPIException(ContentDistributionErrors::DISTRIBUTION_PROVIDER_NOT_FOUND, $distributionProfile->providerType);
			
		$distributionProfile->toObject($dbDistributionProfile);
		$dbDistributionProfile->setPartnerId($this->getPartnerId());
		$dbDistributionProfile->save();
		
		$distributionProfile = KalturaDistributionProfileFactory::createKalturaDistributionProfile($dbDistributionProfile->getProviderType());
		$distributionProfile->fromObject($dbDistributionProfile);
		return $distributionProfile;
	}
	
	/**
	 * Get Distribution Profile by id
	 * 
	 * @action get
	 * @param int $id
	 * @return KalturaDistributionProfile
	 * @throws ContentDistributionErrors::DISTRIBUTION_PROFILE_NOT_FOUND
	 */
	function getAction($id)
	{
		$dbDistributionProfile = DistributionProfilePeer::retrieveByPK($id);
		if (!$dbDistributionProfile)
			throw new KalturaAPIException(ContentDistributionErrors::DISTRIBUTION_PROFILE_NOT_FOUND, $id);
			
		$distributionProfile = KalturaDistributionProfileFactory::createKalturaDistributionProfile($dbDistributionProfile->getProviderType());
		$distributionProfile->fromObject($dbDistributionProfile);
		return $distributionProfile;
	}
	
	/**
	 * Update Distribution Profile by id
	 * 
	 * @action update
	 * @param int $id
	 * @param KalturaDistributionProfile $distributionProfile
	 * @return KalturaDistributionProfile
	 * @throws ContentDistributionErrors::DISTRIBUTION_PROFILE_NOT_FOUND
	 */
	function updateAction($id, KalturaDistributionProfile $distributionProfile)
	{
		$dbDistributionProfile = DistributionProfilePeer::retrieveByPK($id);
		if (!$dbDistributionProfile)
			throw new KalturaAPIException(ContentDistributionErrors::DISTRIBUTION_PROFILE_NOT_FOUND, $id);
		
		if ($distributionProfile->name !== null)
			$distributionProfile->validatePropertyMinLength("name", 1);
			
		$distributionProfile->toUpdatableObject($dbDistributionProfile);
		$dbDistributionProfile->save();
		
		$distributionProfile = KalturaDistributionProfileFactory::createKalturaDistributionProfile($dbDistributionProfile->getProviderType());
		$distributionProfile->fromObject($dbDistributionProfile);
		return $distributionProfile;
	}
	
	/**
	 * Delete Distribution Profile by id
	 * 
	 * @action delete
	 * @param int $id
	 * @throws ContentDistributionErrors::DISTRIBUTION_PROFILE_NOT_FOUND
	 */
	function deleteAction($id)
	{
		$dbDistributionProfile = DistributionProfilePeer::retrieveByPK($id);
		if (!$dbDistributionProfile)
			throw new KalturaAPIException(ContentDistributionErrors::GENERIC_DISTRIBUTION_PROVIDER_NOT_FOUND, $id);

		$dbDistributionProfile->setStatus(DistributionProfileStatus::DELETED);
		$dbDistributionProfile->save();
	}
	
	
	/**
	 * List all distribution providers
	 * 
	 * @action list
	 * @param KalturaDistributionProfileFilter $filter
	 * @param KalturaFilterPager $pager
	 * @return KalturaDistributionProfileListResponse
	 */
	function listAction(KalturaDistributionProfileFilter $filter = null, KalturaFilterPager $pager = null)
	{
		if (!$filter)
			$filter = new KalturaDistributionProfileFilter();
			
		$c = new Criteria();
		$distributionProfileFilter = new DistributionProfileFilter();
		$filter->toObject($distributionProfileFilter);
		
		$distributionProfileFilter->attachToCriteria($c);
		$count = DistributionProfilePeer::doCount($c);
		
		if ($pager)
			$pager->attachToCriteria($c);
		$list = DistributionProfilePeer::doSelect($c);
		
		$response = new KalturaDistributionProfileListResponse();
		$response->objects = KalturaDistributionProfileArray::fromDbArray($list);
		$response->totalCount = $count;
	
		return $response;
	}	
}
