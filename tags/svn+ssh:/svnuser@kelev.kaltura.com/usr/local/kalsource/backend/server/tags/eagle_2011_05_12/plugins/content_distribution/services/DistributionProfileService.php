<?php
/**
 * Distribution Profile service
 *
 * @service distributionProfile
 * @package plugins.contentDistribution
 * @subpackage api.services
 */
class DistributionProfileService extends KalturaBaseService
{
	public function initService($serviceId, $serviceName, $actionName)
	{
		parent::initService($serviceId, $serviceName, $actionName);

		myPartnerUtils::addPartnerToCriteria(new DistributionProfilePeer(), $this->getPartnerId(), $this->private_partner_data, $this->partnerGroup());
		
		if(!ContentDistributionPlugin::isAllowedPartner($this->getPartnerId()))
			throw new KalturaAPIException(KalturaErrors::SERVICE_FORBIDDEN, $this->serviceName.'->'.$this->actionName);
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
		
		$providerType = kPluginableEnumsManager::apiToCore('DistributionProviderType', $distributionProfile->providerType);
		$dbDistributionProfile = DistributionProfilePeer::createDistributionProfile($providerType);
		if(!$dbDistributionProfile)
			throw new KalturaAPIException(ContentDistributionErrors::DISTRIBUTION_PROVIDER_NOT_FOUND, $distributionProfile->providerType);
			
		$distributionProfile->toInsertableObject($dbDistributionProfile);
		$dbDistributionProfile->setPartnerId($this->impersonatedPartnerId);
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
	 * Update Distribution Profile status by id
	 * 
	 * @action updateStatus
	 * @param int $id
	 * @param KalturaDistributionProfileStatus $status
	 * @return KalturaDistributionProfile
	 * @throws ContentDistributionErrors::DISTRIBUTION_PROFILE_NOT_FOUND
	 */
	function updateStatusAction($id, $status)
	{
		$dbDistributionProfile = DistributionProfilePeer::retrieveByPK($id);
		if (!$dbDistributionProfile)
			throw new KalturaAPIException(ContentDistributionErrors::DISTRIBUTION_PROFILE_NOT_FOUND, $id);
		
		$dbDistributionProfile->setStatus($status);
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
			throw new KalturaAPIException(ContentDistributionErrors::DISTRIBUTION_PROFILE_NOT_FOUND, $id);

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
	
	/**
	 * @action listByPartner
	 * @param KalturaPartnerFilter $filter
	 * @param KalturaFilterPager $pager
	 * @return KalturaDistributionProfileListResponse
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
				$c->add(DistributionProfilePeer::PARTNER_ID, $partnerIds, Criteria::IN);
			}
		}
			
		if (is_null($pager))
			$pager = new KalturaFilterPager();
			
		$c->addDescendingOrderByColumn(DistributionProfilePeer::CREATED_AT);
		
		$totalCount = DistributionProfilePeer::doCount($c);
		$pager->attachToCriteria($c);
		$list = DistributionProfilePeer::doSelect($c);
		$newList = KalturaDistributionProfileArray::fromDbArray($list);
		
		$response = new KalturaDistributionProfileListResponse();
		$response->totalCount = $totalCount;
		$response->objects = $newList;
		return $response;
	}
}
