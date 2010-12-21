<?php
/**
 * Entry Distribution service
 *
 * @service entryDistribution
 */
class EntryDistributionService extends KalturaBaseService
{
	public function initService($partnerId, $puserId, $ksStr, $serviceName, $action)
	{
		parent::initService($partnerId, $puserId, $ksStr, $serviceName, $action);

		myPartnerUtils::addPartnerToCriteria(new EntryDistributionPeer(), $this->getPartnerId(), $this->private_partner_data, $this->partnerGroup());
		
		if(!ContentDistributionPlugin::isAllowedPartner($this->getPartnerId()))
			throw new KalturaAPIException(KalturaErrors::SERVICE_FORBIDDEN);
	}
	
	/**
	 * Add new Entry Distribution
	 * 
	 * @action add
	 * @param KalturaEntryDistribution $entryDistribution
	 * @return KalturaEntryDistribution
	 * @throws ContentDistributionErrors::DISTRIBUTION_PROFILE_NOT_FOUND
	 * @throws ContentDistributionErrors::ENTRY_DISTRIBUTION_ALREADY_EXISTS
	 * @throws KalturaErrors::ENTRY_ID_NOT_FOUND
	 */
	function addAction(KalturaEntryDistribution $entryDistribution)
	{
		KalturaLog::debug('entryDistribution before all: ' . print_r($entryDistribution, true));
		$entryDistribution->validatePropertyNotNull("entryId");
		$entryDistribution->validatePropertyNotNull("distributionProfileId");
					
		$dbDistributionProfile = DistributionProfilePeer::retrieveByPK($entryDistribution->distributionProfileId);
		if (!$dbDistributionProfile)
			throw new KalturaAPIException(ContentDistributionErrors::DISTRIBUTION_PROFILE_NOT_FOUND, $entryDistribution->distributionProfileId);
		
		$dbEntry = entryPeer::retrieveByPK($entryDistribution->entryId);
		if (!$dbEntry)
			throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $entryDistribution->entryId);
		
		$dbEntryDistribution = EntryDistributionPeer::retrieveByEntryAndProfileId($entryDistribution->entryId, $entryDistribution->distributionProfileId);
		if($dbEntryDistribution)
			throw new KalturaAPIException(ContentDistributionErrors::ENTRY_DISTRIBUTION_ALREADY_EXISTS, $entryDistribution->entryId, $entryDistribution->distributionProfileId);
		
		KalturaLog::debug('entryDistribution before add: ' . print_r($entryDistribution, true));
		$dbEntryDistribution = kContentDistributionManager::addEntryDistribution($dbEntry, $dbDistributionProfile);
		KalturaLog::debug('entryDistribution before insert: ' . print_r($entryDistribution, true));
		$entryDistribution->toInsertableObject($dbEntryDistribution);
		$dbEntryDistribution->setPartnerId($this->getPartnerId());
		$dbEntryDistribution->setStatus(EntryDistributionStatus::PENDING);
		$dbEntryDistribution->save();
		
		$entryDistribution = new KalturaEntryDistribution();
		$entryDistribution->fromObject($dbEntryDistribution);
		return $entryDistribution;
	}
	
	/**
	 * Get Entry Distribution by id
	 * 
	 * @action get
	 * @param int $id
	 * @return KalturaEntryDistribution
	 * @throws ContentDistributionErrors::ENTRY_DISTRIBUTION_NOT_FOUND
	 */
	function getAction($id)
	{
		$dbEntryDistribution = EntryDistributionPeer::retrieveByPK($id);
		if (!$dbEntryDistribution)
			throw new KalturaAPIException(ContentDistributionErrors::ENTRY_DISTRIBUTION_NOT_FOUND, $id);
			
		$entryDistribution = new KalturaEntryDistribution();
		$entryDistribution->fromObject($dbEntryDistribution);
		return $entryDistribution;
	}
	
	/**
	 * Update Entry Distribution by id
	 * 
	 * @action update
	 * @param int $id
	 * @param KalturaEntryDistribution $entryDistribution
	 * @return KalturaEntryDistribution
	 * @throws ContentDistributionErrors::ENTRY_DISTRIBUTION_NOT_FOUND
	 */
	function updateAction($id, KalturaEntryDistribution $entryDistribution)
	{
		$dbEntryDistribution = EntryDistributionPeer::retrieveByPK($id);
		if (!$dbEntryDistribution)
			throw new KalturaAPIException(ContentDistributionErrors::ENTRY_DISTRIBUTION_NOT_FOUND, $id);
		
		$entryDistribution->toUpdatableObject($dbEntryDistribution);
		$dbEntryDistribution->save();
		
		$entryDistribution = new KalturaEntryDistribution();
		$entryDistribution->fromObject($dbEntryDistribution);
		return $entryDistribution;
	}
	
	/**
	 * Delete Entry Distribution by id
	 * 
	 * @action delete
	 * @param int $id
	 * @throws ContentDistributionErrors::DISTRIBUTION_PROFILE_NOT_FOUND
	 */
	function deleteAction($id)
	{
		$dbEntryDistribution = EntryDistributionPeer::retrieveByPK($id);
		if (!$dbEntryDistribution)
			throw new KalturaAPIException(ContentDistributionErrors::GENERIC_DISTRIBUTION_PROVIDER_NOT_FOUND, $id);

		$dbEntryDistribution->setStatus(EntryDistributionStatus::DELETED);
		$dbEntryDistribution->save();
	}
	
	
	/**
	 * List all distribution providers
	 * 
	 * @action list
	 * @param KalturaEntryDistributionFilter $filter
	 * @param KalturaFilterPager $pager
	 * @return KalturaEntryDistributionListResponse
	 */
	function listAction(KalturaEntryDistributionFilter $filter = null, KalturaFilterPager $pager = null)
	{
		if (!$filter)
			$filter = new KalturaEntryDistributionFilter();
			
		$c = new Criteria();
		$entryDistributionFilter = new EntryDistributionFilter();
		$filter->toObject($entryDistributionFilter);
		
		$entryDistributionFilter->attachToCriteria($c);
		$count = EntryDistributionPeer::doCount($c);
		
		if ($pager)
			$pager->attachToCriteria($c);
		$list = EntryDistributionPeer::doSelect($c);
		
		$response = new KalturaEntryDistributionListResponse();
		$response->objects = KalturaEntryDistributionArray::fromDbArray($list);
		$response->totalCount = $count;
	
		return $response;
	}
	
	/**
	 * Submits Entry Distribution to the remote destination
	 * 
	 * @action submitAdd
	 * @param int $id
	 * @param bool $submitWhenReady
	 * @return KalturaEntryDistribution
	 * @throws ContentDistributionErrors::ENTRY_DISTRIBUTION_NOT_FOUND
	 * @throws ContentDistributionErrors::GENERIC_DISTRIBUTION_PROVIDER_NOT_FOUND
	 */
	function submitAddAction($id, $submitWhenReady = false)
	{
		$dbEntryDistribution = EntryDistributionPeer::retrieveByPK($id);
		if (!$dbEntryDistribution)
			throw new KalturaAPIException(ContentDistributionErrors::ENTRY_DISTRIBUTION_NOT_FOUND, $id);
		
		$distributionProfileId = $dbEntryDistribution->getDistributionProfileId();
		$dbDistributionProfile = DistributionProfilePeer::retrieveByPK($distributionProfileId);
		if(!$dbDistributionProfile)
			throw new KalturaAPIException(ContentDistributionErrors::GENERIC_DISTRIBUTION_PROVIDER_NOT_FOUND, $distributionProfileId);
		
		kContentDistributionManager::submitAddEntryDistribution($dbEntryDistribution, $dbDistributionProfile, $submitWhenReady);

		$dbEntryDistribution->reload();
		$entryDistribution = new KalturaEntryDistribution();
		$entryDistribution->fromObject($dbEntryDistribution);
		return $entryDistribution;
	}

	
	/**
	 * Submits Entry Distribution changes to the remote destination
	 * 
	 * @action submitUpdate
	 * @param int $id
	 * @return KalturaEntryDistribution
	 * @throws ContentDistributionErrors::ENTRY_DISTRIBUTION_NOT_FOUND
	 * @throws ContentDistributionErrors::GENERIC_DISTRIBUTION_PROVIDER_NOT_FOUND
	 */
	function submitUpdateAction($id)
	{
		$dbEntryDistribution = EntryDistributionPeer::retrieveByPK($id);
		if (!$dbEntryDistribution)
			throw new KalturaAPIException(ContentDistributionErrors::ENTRY_DISTRIBUTION_NOT_FOUND, $id);
		
		$distributionProfileId = $dbEntryDistribution->getDistributionProfileId();
		$dbDistributionProfile = DistributionProfilePeer::retrieveByPK($distributionProfileId);
		if(!$dbDistributionProfile)
			throw new KalturaAPIException(ContentDistributionErrors::GENERIC_DISTRIBUTION_PROVIDER_NOT_FOUND, $distributionProfileId);
		
		kContentDistributionManager::submitUpdateEntryDistribution($dbEntryDistribution, $dbDistributionProfile);

		$dbEntryDistribution->reload();
		$entryDistribution = new KalturaEntryDistribution();
		$entryDistribution->fromObject($dbEntryDistribution);
		return $entryDistribution;
	}

	
	/**
	 * Deletes Entry Distribution from the remote destination
	 * 
	 * @action submitDelete
	 * @param int $id
	 * @return KalturaEntryDistribution
	 * @throws ContentDistributionErrors::ENTRY_DISTRIBUTION_NOT_FOUND
	 * @throws ContentDistributionErrors::GENERIC_DISTRIBUTION_PROVIDER_NOT_FOUND
	 */
	function submitDeleteAction($id)
	{
		$dbEntryDistribution = EntryDistributionPeer::retrieveByPK($id);
		if (!$dbEntryDistribution)
			throw new KalturaAPIException(ContentDistributionErrors::ENTRY_DISTRIBUTION_NOT_FOUND, $id);
		
		$distributionProfileId = $dbEntryDistribution->getDistributionProfileId();
		$dbDistributionProfile = DistributionProfilePeer::retrieveByPK($distributionProfileId);
		if(!$dbDistributionProfile)
			throw new KalturaAPIException(ContentDistributionErrors::GENERIC_DISTRIBUTION_PROVIDER_NOT_FOUND, $distributionProfileId);
		
		kContentDistributionManager::submitDeleteEntryDistribution($dbEntryDistribution, $dbDistributionProfile);

		$dbEntryDistribution->reload();
		$entryDistribution = new KalturaEntryDistribution();
		$entryDistribution->fromObject($dbEntryDistribution);
		return $entryDistribution;
	}

	
	/**
	 * Retries last submit action
	 * 
	 * @action retrySubmit
	 * @param int $id
	 * @return KalturaEntryDistribution
	 * @throws ContentDistributionErrors::ENTRY_DISTRIBUTION_NOT_FOUND
	 * @throws ContentDistributionErrors::GENERIC_DISTRIBUTION_PROVIDER_NOT_FOUND
	 */
	function retrySubmitAction($id)
	{
		$dbEntryDistribution = EntryDistributionPeer::retrieveByPK($id);
		if (!$dbEntryDistribution)
			throw new KalturaAPIException(ContentDistributionErrors::ENTRY_DISTRIBUTION_NOT_FOUND, $id);
		
		$distributionProfileId = $dbEntryDistribution->getDistributionProfileId();
		$dbDistributionProfile = DistributionProfilePeer::retrieveByPK($distributionProfileId);
		if(!$dbDistributionProfile)
			throw new KalturaAPIException(ContentDistributionErrors::GENERIC_DISTRIBUTION_PROVIDER_NOT_FOUND, $distributionProfileId);
		
		switch($dbEntryDistribution->getStatus())
		{
			case EntryDistributionStatus::QUEUED:
			case EntryDistributionStatus::SUBMITTING: 
			case EntryDistributionStatus::ERROR_SUBMITTING:
				kContentDistributionManager::submitAddEntryDistribution($dbEntryDistribution, $dbDistributionProfile, false);
				$dbEntryDistribution->reload();
				break;
				
			case EntryDistributionStatus::UPDATING:
			case EntryDistributionStatus::ERROR_UPDATING:
				kContentDistributionManager::submitUpdateEntryDistribution($dbEntryDistribution, $dbDistributionProfile);
				$dbEntryDistribution->reload();
				break;
				
			case EntryDistributionStatus::DELETING:
			case EntryDistributionStatus::ERROR_DELETING:
				kContentDistributionManager::submitDeleteEntryDistribution($dbEntryDistribution, $dbDistributionProfile);
				$dbEntryDistribution->reload();
				break;
				
			case EntryDistributionStatus::PENDING:
			case EntryDistributionStatus::READY:
			case EntryDistributionStatus::DELETED:
				break;
		}

		$entryDistribution = new KalturaEntryDistribution();
		$entryDistribution->fromObject($dbEntryDistribution);
		return $entryDistribution;
	}
}
